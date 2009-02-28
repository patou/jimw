<?php
class Jimw_Admin_View_Helper_Url
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
    public function url (array $urlOptions = array(), $name = null, $reset = false, $admin = true)
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
                        $name = 'get';
                    } else {
                        Jimw_Debug::display_exception($e);
                    }
            }
        }
        $route = $router->getRoute($name);
        $this->request = $ctrl->getRequest();

        $url = $this->buildUrl($urlOptions, $admin);
        $url .= ltrim($route->assemble($urlOptions, $reset), '/');
        return $url;
    }

    private function buildUrl($urlOptions = array(), $admin = true) {
        if (! JIMW_URL_REWRITING) {
            $basePath = $this->request->getBaseUrl();
            $baseUrl = substr($this->request->getBaseUrl(), strlen($basePath));
            $url = rtrim($basePath, '/') . '/?' . ltrim($baseUrl, '/');
        } else {
            $url = rtrim($this->request->getBaseUrl(), '/') . '/';
        }
        if (!$admin) {
            $url = rtrim(str_replace('admin', '/', $url), '/') . '/';
        }
        $domain = Zend_Registry::get('current_domain');
        $site = Zend_Registry::get('site');
        if (isset($urlOptions['site']) && $site->id != $urlOptions['site']) {
                if ($new_site = $this->siteTable->findCache($urlOptions['site'])->current()) {
                   $new_domain = $this->domainTable->findCache($new_site->domain_id)->current();
                   $new_domain_url = $new_domain->toUrl('', false);
                   if ($new_domain && $new_domain_url != $domain->toUrl('', false)) {
                        $url = rtrim($new_domain_url, '/') . $url;
                   }
                   $domain = $new_domain;
                }
        }
        if (! empty($domain->path))
            $url .= $domain->path . '/';
        return $url;
    }
}
?>
