<?php
class Jimw_Script_Plugin_Script extends Zend_Controller_Plugin_Abstract {
    private $path = '';
    private $arguments = array();
    public function __construct() {
        $site_path = Zend_Registry::get('site_path', '');
        $this->path = JIMW_ROOT . $site_path . '/script/';
        //parent::__construct();
    }

    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->__call('routeStartup', array($request));
    }

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->__call('routeShutdown', array($request));
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->__call('dispatchLoopStartup', array($request));
    }

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->__call('preDispatch', array($request));
    }

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->__call('postDispatch', array($request));
    }

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->__call('dispatchLoopShutdown', array());
    }
    
    public function __call($name, $arguments) {
        if (file_exists($this->path . $name . '.php')) {
             Jimw_Debug::display('Exec : ' . $this->path . $name . '.php');         
            $this->arguments = $arguments;
            include($this->path . $name . '.php');
        }
    }
   
}
?>