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
	 * The Dispatch var
	 *
	 * @var Jimw_Admin_Dispatch
	 */
	private $dispatch;
	/**
	 * The global router
	 *
	 * @var Jimw_Global_Router
	 */
	private $router;
	
	private $frontcontroller;

	public function __construct() {
		$this->request = new Jimw_Global_Request();
		$this->response = new Zend_Controller_Response_Http();
		$this->router = new Jimw_Global_Router();
	    $this->dispatch = new Jimw_Site_Dispatch();
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
		include (JIMW_REP . '/lang/fr/common.php');
		$trans = new Zend_Translate('array', $lang, 'fr');
		return $trans;
	}

	public function initView() {
		$view = new Jimw_Site_View();
		$view->addFilterPath(JIMW_REP_LIB . 'Jimw/Site/View/Filter', 'Jimw_Site_View_Filter_');
		$view->addHelperPath(JIMW_REP_LIB . 'Jimw/Site/View/Helper', 'Jimw_Site_View_Helper_');
		$view->addScriptPath(JIMW_REP_LIB . 'Jimw/view/scripts');
		$view->setTranslate ($this->initTranslate());
		$viewRenderer = new Jimw_Site_View_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		return $view;
	}

	private function _registerPlugin() {
		$dir = new DirectoryIterator(JIMW_REP_LIB . 'Jimw/Plugin');
		if ($dir->valid()) {
			foreach ($dir as $plugin) {
				if (!$dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
					include_once (JIMW_REP_LIB . 'Jimw/Plugin/' . $plugin);
					$class_name = "Jimw_Plugin_${plugin_info[1]}";
					if (class_exists($class_name)) {
						$class = new $class_name();
						$this->frontcontroller->registerPlugin($class);
					}
				}
			}
		}
	}
	
	private function _registerModulePlugin() {
		$this->frontcontroller->setModuleControllerDirectoryName('Controller');
		$this->frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
		$db = Zend_Registry::get('db');
		$modules = new Jimw_Site_Module();
		$modules_list = $modules->fetchAll();
		foreach ($modules_list as $module) {
			$name = $module->path;
			$Name = ucfirst($name);
			$path = JIMW_REP_MODULE . $name . '/Plugin/';
			if (file_exists($path)) {
				$dir = new DirectoryIterator($path);
				if ($dir->valid()) {
					foreach ($dir as $plugin) {
						if (!$dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
							include_once ($path . $plugin);
							$class_name = "Jimw_${Name}_Plugin_${plugin_info[1]}";
							if (class_exists($class_name)) {
								$class = new $class_name();
								$this->frontcontroller->registerPlugin($class);
							}
						}
					}
				}
			}
		}
	}
	
	public function run () {
		$this->request = $this->router->route($this->request);
		$this->initView();
		$this->frontcontroller = Zend_Controller_Front::getInstance();
//		$this->frontcontroller->throwExceptions(true);
		$this->frontcontroller->setRequest($this->request);
		$router = new Jimw_Site_Router();
		$router->removeDefaultRoutes();
		$router->addRoute('alias', new Jimw_Site_Route_Alias(array(), $this->dispatch, $this->request));
		$router->addRoute('ext', new Jimw_Site_Route_Module(array(), $this->dispatch, $this->request, true));
		$router->addRoute('get', new Jimw_Site_Route_Get(array()));
		$this->frontcontroller->setRouter($router);
		$this->frontcontroller->setDispatcher($this->dispatch);
		$this->frontcontroller->setControllerDirectory(JIMW_REP_LIB . 'Jimw/Controller/');
		$this->_registerPlugin();
		$this->_registerModulePlugin();
		$this->frontcontroller->setResponse($this->response);
		Jimw_Global_Layout::startMvc();
		$this->frontcontroller->dispatch($this->request, $this->response);
		//Zend_Debug::dump($this->frontcontroller);
	}
}
