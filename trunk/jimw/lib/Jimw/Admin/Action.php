<?php
/**
 * Jimw_Admin_Action
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
abstract class Jimw_Admin_Action extends Zym_Controller_Action_Abstract
{
    public function init ()
    {
        $request = $this->getRequest();
        $tree = $request->getTree();
        $ext = $request->getParam('format', JIMW_DEFAULT_EXT);
        Zend_Registry::set('format', $ext);
        $this->getHelper('ViewRenderer')->setViewSuffix($ext);
        $this->getHelper('Layout')->setViewSuffix($ext);
        $site = Zend_Registry::get('site');
        $this->view->site = $site;
        $this->view->request = $request;
        $this->view->path = $request->getBaseUrl() . '/public';
        $this->view->path_common = str_replace('/admin', JIMW_URL_PUBLIC_COMMON_PATH, $request->getBaseUrl());
        $this->view->tree = $tree;
        $this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages();
        $this->initModule();
    }
    public function deleteAction ()
    {
        $this->_forward('index', 'tree', 'default');
    }
    public function initModule ()
    {}
    /**
     * Translate string
     *
     * @param string $messageId
     * @param string $local
     * @return string
     */
    protected function _ ($messageId)
    {
        $options = func_get_args();
        return call_user_func_array(array($this->view->translate() , 'translate'), $options);
    }
    /**
     * Get the ACL
     * @return Jimw_Acl
     */
    protected function getAcl ()
    {
        if ($this->_auth == null) {
            $this->_auth = Zend_Registry::get('Zend_Acl');
        }
        return $this->_auth;
    }
    /**
     * Check if the current user is allowed to access to this ressource and privilege
     *
     */
    protected function isRoleAllowed ($resource = null, $privilege = null)
    {
        $acl = $this->getAcl();
        return ($acl != null) ? $acl->isRoleAllowed($resource, $privilege) : false;
    }
    /**
     * @var Jimw_Acl
     */
    protected $_auth = null;
    /**
     * Check if the ressource is allowed for the given privilege
     * @param $resource
     * @param $privilege
     * @throw Jimw_Acl_Exception if the role isn't allowed
     * @return void
     */
    protected function checkRoleAllowed ($resource = null, $privilege = null)
    {
        if (! $this->isRoleAllowed($resource, $privilege)) {
            throw new Jimw_Acl_Exception();
        }
    }
    /**
     * 
     * @return array
     */
    protected function getUser ()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            return $identity;
        }
        return null;        
    }
}