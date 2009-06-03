<?php
class Jimw_Plugin_Perms extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Jimw_Acl
     */
    protected $auth;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $auth = new Jimw_Acl();
        $auth->init();
        Jimw_Debug::dump($auth);
        Zend_Registry::set('Zend_Acl', $auth);
        $this->auth = $auth;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ($this->auth != null && !$this->auth->isRoleAllowed('public', 'view') && 
        !in_array($request->getControllerName(),array('auth', 'error')))
        {
            $request->setActionName('perms');
            $request->setControllerName('error');
            $request->setModuleName('default');
        }
    }
}
?>