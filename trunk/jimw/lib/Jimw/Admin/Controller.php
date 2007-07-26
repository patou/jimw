<?php
/**
 * Jimw_Global_Controler
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Jimw_Admin_Controller
{
	/**
	 * The request var
	 *
	 * @var Jimw_Global_Request
	 */
	private $request;

	/**
	 * The responce var
	 *
	 * @var Zend_Controller_Response_Http
	 */
	private $response;

	/**
	 * The global router
	 *
	 * @var Jimw_Global_Router
	 */
	private $router;

	public function __construct() {
		$this->request = new Jimw_Global_Request();
		$this->response = new Zend_Controller_Response_Http();
		$this->router = new Jimw_Global_Router();
	}

	/**
	 * Get the request var
	 *
	 * @var Jimw_Global_Request
	 */
	public function getRequest () {
		return $this->request;
	}


	/**
	 * Get the request var
	 *
	 * @var Zend_Controller_Response_Http
	 */
	public function getResponse () {
		return $this->response;
	}

	public function initTranslate () {
		$trans = new Zend_Translate('array', array(), 'fr');
		return $trans;
	}

	public function initView() {
		$view = new Jimw_Site_View();
		$view->addBasePath(JIMW_REP_LIB . 'Jimw/Admin/views/', 'Jimw_Admin_View');
		$view->setTranslate ($this->initTranslate());
		$viewRenderer = new Jimw_Site_View_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		return $view;
	}

	public function run () {
		$this->request = $this->router->route($this->request);
		$view = $this->initView();
		$frontcontroller = Zend_Controller_Front::getInstance();
		$frontcontroller->throwExceptions(true);
		$frontcontroller->setRequest($this->request);
		$frontcontroller->setControllerDirectory(JIMW_REP_LIB . 'Jimw/Admin/Controller/');
		$frontcontroller->setResponse($this->response);
		$frontcontroller->setModuleControllerDirectoryName('Admin/Controller');
		$frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
		$frontcontroller->dispatch($this->request, $this->response);
		//Zend_Debug::dump($frontcontroller);
	}
}
?>