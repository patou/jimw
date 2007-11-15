<?php

class Jimw_Admin_View_Helper_Url {
	public function url(array $urlOptions = array(), $name = null, $reset = false) {
 
        $ctrl = Zend_Controller_Front::getInstance();
        
        $router = $ctrl->getRouter();
        
        if (empty($name)) {
            try {
                $name = $router->getCurrentRouteName();
            } catch (Zend_Controller_Router_Exception $e) {
                if ($router->hasRoute('default')) {
                    $name = 'default';
                }
            }
        } 

        $route = $router->getRoute($name);
        
        $request = $ctrl->getRequest();
        
        if (defined('JIMW_NO_REWRITE_URL')) {
        	$basePath = $request->getBaseUrl();
        	$baseUrl = substr($request->getBaseUrl(), strlen($basePath));
        	$url = rtrim($basePath, '/') . '/?' . ltrim($baseUrl, '/');
        }
        else {
        	$url = rtrim($request->getBaseUrl(), '/') . '/';
        }
        $url .= ltrim($route->assemble($urlOptions, $reset), '/');
        
        return $url;
	}
}
?>
