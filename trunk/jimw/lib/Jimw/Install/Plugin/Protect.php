<?php

class Jimw_Install_Plugin_Protect extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
	    if ($request->getControllerName () != 'error') {
		    if (defined('JIMW_INSTALL_PROTECT_DOMAIN')) {
		        if ($request->getSubDomain() .'.'.$request->getDomainName() != JIMW_INSTALL_PROTECT_DOMAIN)
		            throw new Jimw_Install_Exception('Install administration not allowed on this domain');
		    }
			$session = new Zend_Session_Namespace ( 'Install' );
			if (! isset ( $session->protect_file ) || ! file_exists ( JIMW_INSTALL_PROTECTDIR . $session->protect_file )) {
				$request->setControllerName ( 'protect' );
				$request->setActionName ( 'index' );
				$request->setModuleName ( 'default' );
			}
		}
	}
}
