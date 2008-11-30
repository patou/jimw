<?php
class Jimw_Admin_View_Helper_Url
{
    private $request = null;
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
        $trees = new Jimw_Site_Tree();
        if (isset($urlOptions['site']) && $site->id != $urlOptions['site']) {
                $sites = new Jimw_Site_Site();
                if ($new_site = $sites->findCache($urlOptions['site'])->current()) {
                   $new_domain = $new_site->findParentJimw_Site_Domain();
                   if ($new_domain && $new_domain->toUrl('', false) != $domain->toUrl('', false)) {
                        $url = rtrim($new_domain->toUrl('', false), '/') . $url;
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
