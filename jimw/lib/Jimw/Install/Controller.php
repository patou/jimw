<?php
/**
 * Jimw_Install_Controler
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Controller.php 183 2008-03-13 16:17:31Z logisim $
 */
class Jimw_Install_Controller
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
    public function __construct ()
    {
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
    public function getRequest ()
    {
        return $this->request;
    }
    /**
     * Get the request var
     *
     * @var Zend_Controller_Response_Http
     */
    public function getResponse ()
    {
        return $this->response;
    }
    /**
     * Initialize the translation
     */
    public function initTranslate ()
    {
        $trans = new Zend_Translate('array', JIMW_REP_LANG . JIMW_LANG . '/common.php', JIMW_LANG);
        Zend_Form::setDefaultTranslator($trans);
        Zend_Registry::set('Zend_Translate', $trans);
        return $trans;
    }
    /**
     * Initialize the view
     */
    public function initView ()
    {
        $view = new Jimw_Site_View();
        $view->addBasePath(JIMW_REP_LIB . 'Jimw/Install/views/', 'Jimw_Install_View');
        //$view->addHelperPath(JIMW_REP_LIB . 'Jimw/Admin/views/helpers', 'Jimw_Admin_View_Helper')
        $view->addScriptPath(JIMW_REP_INSTALL);
        $view->setTranslate($this->initTranslate());
        $viewRenderer = new Jimw_Site_View_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        return $view;
    }
    public function _registerPlugins ()
    {
        $dir = new DirectoryIterator(JIMW_REP_LIB . 'Jimw/Install/Plugin');
        if ($dir->valid()) {
            foreach ($dir as $plugin) {
                if (! $dir->isDot() && ereg("^(.*)\.php$", $plugin, $plugin_info)) {
                    include_once (JIMW_REP_LIB . 'Jimw/Install/Plugin/' . $plugin);
                    $class_name = "Jimw_Install_Plugin_${plugin_info[1]}";
                    if (class_exists($class_name)) {
                        $class = new $class_name();
                        $this->frontcontroller->registerPlugin($class);
                    }
                }
            }
        }
    }
    public function connect ()
    {
        global $jimw_config_db;
        try {
            if (JIMW_DEBUG_MODE) {
                $jimw_config_db['profiler'] = true;
            }
            $db = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);
            Zend_Registry::set('db_global', $db);
        } catch (Zend_Db_Exception $e) {
            Jimw_Debug::display_exception($e);
        }
    }
    /**
     * Run the controller
     *
     */
    public function run ()
    {
        //$this->request = $this->router->route($this->request);
        $this->initView();
        $this->frontcontroller = Zend_Controller_Front::getInstance();
        $this->connect();
        $this->frontcontroller->setBaseUrl(JIMW_URL_INSTALL_PATH);
        $router = $this->frontcontroller->getRouter();
        //		$this->frontcontroller->throwExceptions(true);
        $this->frontcontroller->setRequest($this->request);
        $this->frontcontroller->setResponse($this->response);
        $this->frontcontroller->setDispatcher($this->dispatch);
        $this->_registerPlugins();
        $router->addRoute('get', new Jimw_Site_Route_Get(array()));
        $this->frontcontroller->addControllerDirectory(JIMW_REP_LIB . 'Jimw/Install/Controller/', 'default');
        Jimw_Global_Layout::startMvc();
        $this->frontcontroller->dispatch($this->request, $this->response);
        //Zend_Debug::dump($this->frontcontroller);
    }
}
