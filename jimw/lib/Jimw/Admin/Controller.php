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
class Jimw_Admin_Controller extends Jimw_Controller
{
    public function __construct ()
    {
        $this->request = new Jimw_Global_Request();
        $this->response = new Zend_Controller_Response_Http();
        $this->router = new Jimw_Global_Router();
        $this->dispatch = new Jimw_Admin_Dispatch();
    }

    public function initView ()
    {
        $view = parent::initView();
        $view->addBasePath(JIMW_REP_LIB . 'Jimw/Admin/views/', 'Jimw_Admin_View');
        $view->addScriptPath(JIMW_REP_ADMIN_PUBLIC);
        return $view;
    }

    public function _registerPlugins ()
    {
        $dir = new DirectoryIterator(JIMW_REP_LIB . 'Jimw/Admin/Plugin');
        if ($dir->valid()) {
            foreach ($dir as $plugin) {
                if (! $dir->isDot() && preg_match("/^(.*)\.php$/", $plugin, $plugin_info)) {
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

    private function _registerModulePlugin ()
    {
        $this->frontcontroller->setModuleControllerDirectoryName('Admin/Controller');
        $this->frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
        $db = Zend_Registry::get('db');
        $modules = new Jimw_Site_Module();
        $modules_list = $modules->fetchAll();
        foreach ($modules_list as $module) {
            $name = $module->path;
            $Name = ucfirst($name);
            $path = JIMW_REP_MODULE . $name;
            if (file_exists($path . '/Admin/Plugin/')) {
                $dir = new DirectoryIterator($path . '/Admin/Plugin/');
                if ($dir->valid()) {
                    foreach ($dir as $plugin) {
                        if (! $dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
                            include_once ($path . '/Admin/Plugin/' . $plugin);
                            $class_name = "Jimw_${Name}_Admin_Plugin_${plugin_info[1]}";
                            if (class_exists($class_name)) {
                                $class = new $class_name();
                                $this->frontcontroller->registerPlugin($class);
                            }
                        }
                    }
                }
            }
            if (file_exists($path . '/Admin/views/helpers')) {
                $this->view->addHelperPath($path . '/Admin/views/helpers', $Name . '_Admin_View_Helper');
            }
            if (file_exists($path .  '/lang')) { //Add Lang file
                $this->translate->addTranslation($path . '/lang');
            }
            Zend_Loader_Autoloader::getInstance()->registerNamespace($name.'_');
        }
    }

    public function run ()
    {
        $this->request = $this->router->route($this->request);
        $this->initView();
        $this->frontcontroller = Zend_Controller_Front::getInstance();
        $this->frontcontroller->setBaseUrl(JIMW_URL_ADMIN_PATH);
        $router = $this->frontcontroller->getRouter();
        //		$this->frontcontroller->throwExceptions(true);
        $this->frontcontroller->setRequest($this->request);
        $this->frontcontroller->setResponse($this->response);
        $this->frontcontroller->setDispatcher($this->dispatch);
        $this->_registerPlugins();
        $this->_registerModulePlugin();
        $this->frontcontroller->addControllerDirectory(JIMW_REP_LIB . 'Jimw/Admin/Controller/', 'default');
        $router->addRoute('alias', new Jimw_Site_Route_Alias(array(), $this->dispatch, $this->request));
        $router->addRoute('format', new Jimw_Site_Route_Module(array(), $this->dispatch, $this->request, false));
        $router->addRoute('get', new Jimw_Site_Route_Get(array()));
        Jimw_Global_Layout::startMvc();
        $this->initCache();
        $this->frontcontroller->dispatch($this->request, $this->response);
        //Zend_Debug::dump($this->frontcontroller);
    }
}
