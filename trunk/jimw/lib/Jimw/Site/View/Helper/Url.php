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
    public function url (array $urlOptions = array(), $name = null, $reset = false)
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
        $url = $this->buildUrl($urlOptions);
        $url .= ltrim($route->assemble($urlOptions, $reset), '/');


        return $url;
    }

    private function buildUrl($urlOptions = array()) {
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
        $aliasKey = $this->getView()->getRequest()->getAliasKey();
        if (isset($urlOptions[$aliasKey])) {
            $tree = $this->treeTable->findAlias($urlOptions[$aliasKey]);
            if (count($tree)) {
                $site_id = $tree->current()->site_id;
                if ($site_id != $site->id) {
                    if ($new_site = $this->siteTable->findCache($site_id)->current()) {
                        $new_domain = $this->domainTable->findCache($new_site->domain_id)->current();
                        $new_domain_url = rtrim($new_domain->toUrl('', false), '/');
                        if ($new_domain && $new_domain_url != $domainUrl) {
                            $url = $new_domain_url . $url;
                        }
                        $domain = $new_domain;
                    }
                }
            }
        }
        if (! empty($domain->path))
            $url .= $domain->path . '/';
        return $url;
    }
}