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
class Jimw_Global_Controller extends Jimw_Controller
{
    public function __construct ()
    {
        $this->request = new Jimw_Global_Request();
        $this->response = new Zend_Controller_Response_Http();
        $this->router = new Jimw_Global_Router();
        $this->dispatch = new Jimw_Site_Dispatch();
    }

    public function initView ()
    {
        $view = parent::initView();
        $view->addFilterPath(JIMW_REP_LIB . 'Jimw/Site/View/Filter', 'Jimw_Site_View_Filter_');
        $view->addHelperPath(JIMW_REP_LIB . 'Jimw/Site/View/Helper', 'Jimw_Site_View_Helper_');
        $view->addScriptPath(JIMW_REP_LIB . 'Jimw/view/scripts');
        return $view;
    }

    private function _registerPlugin ()
    {
        $dir = new DirectoryIterator(JIMW_REP_LIB . 'Jimw/Plugin');
        if ($dir->valid()) {
            foreach ($dir as $plugin) {
                if (! $dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
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
    private function _registerModulePlugin ()
    {
        $this->frontcontroller->setModuleControllerDirectoryName('Controller');
        $this->frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
        $db = Zend_Registry::get('db');
        $modules = new Jimw_Site_Module();
        $modules_list = $modules->fetchAll();
        foreach ($modules_list as $module) {
            $name = $module->path;
            $Name = ucfirst($name);
            $path = JIMW_REP_MODULE . $name;
            if (file_exists($path . '/Plugin/')) { //Add plugins
                $dir = new DirectoryIterator($path . '/Plugin/');
                if ($dir->valid()) {
                    foreach ($dir as $plugin) {
                        if (! $dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
                            include_once ($path . '/Plugin/' . $plugin);
                            $class_name = "Jimw_${Name}_Plugin_${plugin_info[1]}";
                            if (class_exists($class_name)) {
                                $class = new $class_name();
                                $this->frontcontroller->registerPlugin($class);
                            }
                        }
                    }
                }
            }
            if (file_exists($path .  '/views/helpers')) { //Add Helper
                $this->view->addHelperPath($path .  '/views/helpers', $Name . '_View_Helper');
            }
            if (file_exists($path .  '/lang')) { //Add Lang file
                $this->translate->addTranslation($path . '/lang');
            }
        }
    }

    public function run ()
    {
        $this->request = $this->router->route($this->request);
        $this->initView();
        $this->frontcontroller = Zend_Controller_Front::getInstance();
        //		$this->frontcontroller->throwExceptions(true);
        $this->frontcontroller->setRequest($this->request);
        $router = new Jimw_Site_Router();
        $router->removeDefaultRoutes();
        $router->addRoute('alias', new Jimw_Site_Route_Alias(array(), $this->dispatch, $this->request));
        $router->addRoute('format', new Jimw_Site_Route_Module(array(), $this->dispatch, $this->request, true));
        $router->addRoute('get', new Jimw_Site_Route_Get(array()));
        $router->addRoute('sitemap.xml', new Zend_Controller_Router_Route_Static('sitemap.xml', array('controller' => 'seo', 'action' => 'sitemap', 'module' => 'default', 'format' => 'xml')));
        $router->addRoute('sitemap.phtml', new Zend_Controller_Router_Route_Static('sitemap.phtml', array('controller' => 'seo', 'action' => 'sitemap', 'module' => 'default', 'format' => 'phtml')));
        $router->addRoute('robots.txt', new Zend_Controller_Router_Route_Static('robots.txt', array('controller' => 'seo', 'action' => 'robots', 'module' => 'default', 'format' => 'txt')));
        $router->addRoute('favicon.ico', new Zend_Controller_Router_Route_Static('favicon.ico', array('controller' => 'seo', 'action' => 'favicon', 'module' => 'default', 'format' => 'ico')));
        $this->frontcontroller->setRouter($router);
        $this->frontcontroller->setDispatcher($this->dispatch);
        $this->frontcontroller->setControllerDirectory(JIMW_REP_LIB . 'Jimw/Controller/');
        $this->_registerPlugin();
        $this->_registerModulePlugin();
        $this->frontcontroller->setResponse($this->response);
        Jimw_Global_Layout::startMvc();
        $this->initCache();
        $this->frontcontroller->dispatch($this->request, $this->response);
        //Zend_Debug::dump($this->frontcontroller);
    }
}
