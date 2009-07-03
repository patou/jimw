<?php
/**
 * Jimw_Site_View_Helper_Url
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_View_Helper_Url extends Zym_View_Helper_Abstract
{
    private $request = null;
    private $treeTable = null;
    private $siteTable = null;
    private $domainTable = null;

    public function __construct() {
        $this->treeTable = new Jimw_Site_Tree();
        $this->siteTable = new Jimw_Site_Site();
        $this->domainTable = new Jimw_Site_Domain();
    }
    public function url (array $urlOptions = array(), $name = null, $reset = false, $fullurl = false)
    {
        $ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();
        if (empty($name)) {
            try {
                $name = $router->getCurrentRouteName();
            } catch (Zend_Controller_Router_Exception $e) {
                if ($router->hasRoute('default')) {
                    $name = 'default';
                } else
                    if ($router->hasRoute('get')) {
                        $name = 'default';
                    } else {
                        Jimw_Debug::display_exception($e);
                    }
            }
        }
        $route = $router->getRoute($name);
        $this->request = $ctrl->getRequest();
        // Build the url
        $url = $this->buildUrl($urlOptions, $fullurl);
        $url .= ltrim($route->assemble($urlOptions, $reset), '/');
        
        return $url;
    }

    private function buildUrl($urlOptions = array(), $fullurl) {
        if (defined('JIMW_NO_REWRITE_URL')) {
            $basePath = $this->request->getBasePath();
            $baseUrl = substr($this->request->getBaseUrl(), strlen($basePath));
            $url = rtrim($basePath, '/') . '/?' . ltrim($baseUrl, '/');
        } else {
            $url = rtrim($this->request->getBaseUrl(), '/') . '/';
        }
        $site = Zend_Registry::get('site');
        $domain = Zend_Registry::get('current_domain');
        $domainUrl = rtrim($domain->toUrl('', false), '/');
        $aliasKey = $this->request->getAliasKey();
        if (isset($urlOptions[$aliasKey])) { // If the alias is know, search the Domain via the site_id tree
            $tree = $this->treeTable->findAlias($urlOptions[$aliasKey])->current();
            if ($tree != null) {
                $site_id = $tree->site_id;
                if ($site_id != $site->id) { // If the site isn't the current site search the domain
                    if ($new_site = $this->siteTable->findCache($site_id)->current()) {
                        $new_domain = $this->domainTable->findCache($new_site->domain_id)->current();
                        if ($new_domain) {
                            $new_domain_url = rtrim($new_domain->toUrl('', false), '/');
                            if ($new_domain_url != $domainUrl) { // If the url id different than the current domain
                                $url = $new_domain_url . $url;
                            }
                            $domain = $new_domain;
                        }
                    }
                }
            }
        }
        if ($fullurl == true && strpos($url, "http://") === false) {
            if (strpos($domainUrl, '*') !== false) { // If it's a catch all domain get the default domain site (a domain site can't have a catch all)
                $url = $site->findParentJimw_Site_Domain()->toUrl($url, false);
            }
            else {                       
                $url = $domainUrl . $url;
            }
        }
        if (! empty($domain->path))
            $url .= trim($domain->path, '/') . '/';
        return $url;
    }
}