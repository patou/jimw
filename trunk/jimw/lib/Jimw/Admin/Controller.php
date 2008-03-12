<?php
/**
 * Jimw_Admin_Controler
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
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
		$this->dispatch = new Jimw_Admin_Dispatch();
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
		include (JIMW_REP_LANG . JIMW_LANG . '/common.php');
		$trans = new Zend_Translate('array', $lang, JIMW_LANG);
		return $trans;
	}

	public function initView() {
		$view = new Jimw_Site_View();
		$view->addBasePath(JIMW_REP_LIB . 'Jimw/Admin/views/', 'Jimw_Admin_View');
		$view->addScriptPath(JIMW_REP_PUBLIC);
		$view->setTranslate ($this->initTranslate());
		$viewRenderer = new Jimw_Site_View_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		return $view;
	}

	public function _registerPlugins() {
		$dir = new DirectoryIterator(JIMW_REP_LIB . 'Jimw/Admin/Plugin');
		if ($dir->valid()) {
			foreach ($dir as $plugin) {
				if (!$dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
					include_once (JIMW_REP_LIB . 'Jimw/Admin/Plugin/' . $plugin);
					$class_name = "Jimw_Admin_Plugin_${plugin_info[1]}";
					if (class_exists($class_name)) {
						$class = new $class_name();
						$this->frontcontroller->registerPlugin($class);
					}
				}
			}
		}
	}
	
	private function _registerModulePlugin() {
		$this->frontcontroller->setModuleControllerDirectoryName('Admin/Controller');
		$this->frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
		$db = Zend_Registry::get('db');
		$modules = new Jimw_Site_Module();
		$modules_list = $modules->fetchAll();
		foreach ($modules_list as $module) {
			$name = $module->path;
			$Name = ucfirst($name);
			$path = JIMW_REP_MODULE . $name . '/Admin/Plugin/';
			if (file_exists($path)) {
				$dir = new DirectoryIterator($path);
				if ($dir->valid()) {
					foreach ($dir as $plugin) {
						if (!$dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
							include_once ($path . $plugin);
							$class_name = "Jimw_${Name}_Admin_Plugin_${plugin_info[1]}";
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
		$this->frontcontroller->setBaseUrl(JIMW_URL_ADMIN_PATH);
		$router = $this->frontcontroller->getRouter();
//		$this->frontcontroller->throwExceptions(true);
		$this->frontcontroller->setRequest($this->request);
		$this->frontcontroller->setControllerDirectory(JIMW_REP_LIB . 'Jimw/Admin/Controller/');
		$this->frontcontroller->setResponse($this->response);
		$this->frontcontroller->setDispatcher($this->dispatch);
		$this->_registerPlugins();
		$this->_registerModulePlugin();
		$router->addRoute('ext', new Jimw_Site_Route_Module(array(), $this->dispatch, $this->request, false));
		$router->addRoute('get', new Jimw_Site_Route_Get(array()));
		$this->frontcontroller->dispatch($this->request, $this->response);
		//Zend_Debug::dump($this->frontcontroller);
	}
}
