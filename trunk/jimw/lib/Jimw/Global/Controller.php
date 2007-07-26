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

class Jimw_Global_Controller
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
		$view->addFilterPath(JIMW_REP_LIB . '/Jimw/Admin/View/Filter', 'Jimw_Admin_View_Filter_');
		$view->addHelperPath(JIMW_REP_LIB . '/Jimw/Admin/View/Helper', 'Jimw_Admin_View_Helper_');
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
		$router = new Jimw_Site_Router();
		$frontcontroller->setRouter($router);
		$frontcontroller->setControllerDirectory(JIMW_REP_LIB . 'Jimw/Controller/');
		$frontcontroller->setResponse($this->response);
		$frontcontroller->setModuleControllerDirectoryName('Controller');
		$frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
		//$frontcontroller->registerPlugin(new Jimw_Site_Plugins_GlobalRender ());
		$frontcontroller->setDispatcher(new Jimw_Site_Dispatch ());
		$frontcontroller->dispatch($this->request, $this->response);
		//Zend_Debug::dump($frontcontroller);
	}
}
?>