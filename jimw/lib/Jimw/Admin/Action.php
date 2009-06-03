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
    public function _($messageId)
    {
        $options = func_get_args();
        return call_user_func_array(array($this->view->translate(), 'translate'), $options);
    }

    /**
     * Check if the current user is allowed to access to this ressource and privilege
     *
     */
    public function isRoleAllowed($resource = null, $privilege = null) {
        if ($this->_auth == null) {
            $this->_auth = Zend_Registry::get('Zend_Acl');
        }
        return ($this->_auth != null) ? $this->_auth->isRoleAllowed($resource, $privilege) : false;
    }
    /**
     * @var Jimw_Acl
     */
    protected $_auth = null;

    public function checkRoleAllowed($resource = null, $privilege = null) {
        if (!$this->isRoleAllowed($resource, $privilege)) {
            throw new Jimw_Acl_Exception();
        }
    }
}