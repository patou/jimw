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
            $basePath = $this->request->getBaseUrl();
            $baseUrl = substr($this->request->getBaseUrl(), strlen($basePath));
            $url = rtrim($basePath, '/') . '/?' . ltrim($baseUrl, '/');
        } else {
            $url = rtrim($this->request->getBaseUrl(), '/') . '/';
        }
        $domain = Zend_Registry::get('current_domain');
        $site = Zend_Registry::get('site');
        $trees = new Jimw_Site_Tree();
        $aliasKey = $this->getView()->getRequest()->getAliasKey();
        if (isset($urlOptions[$aliasKey])) {
            $tree = $trees->findAlias($urlOptions[$aliasKey]);
            if (count($tree)) {
                $site_id = $tree->current()->site_id;
                if ($site_id != $site->id) {
                    $sites = new Jimw_Site_Site();
                    if ($new_site = $sites->findCache($site_id)->current()) {
                        $new_domain = $new_site->findParentJimw_Site_Domain();
                        if ($new_domain && $new_domain->toUrl('', false) != $domain->toUrl('', false)) {
                            $url = rtrim($new_domain->toUrl('', false), '/') . $url;
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