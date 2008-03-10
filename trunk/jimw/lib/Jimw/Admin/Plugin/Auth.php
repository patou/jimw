<?php

/**
 *
 */
class Jimw_Admin_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$auth = Zend_Auth::getInstance();

		if (!$auth->hasIdentity()) {
			$request->setActionName('login');
			$request->setControllerName('auth');
			$request->setModuleName('default');
		}
	}
}
