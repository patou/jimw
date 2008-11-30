<?php
/**
 * PermController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class PermController extends Jimw_Admin_Action
{
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $this->_forward('list');
    }
    public function listAction ()
    {
        $perms = new Jimw_Site_Perm();
        $this->view->perms_list = $perms->fetchAll();
    }
    public function editAction ()
    {
        $id = $this->_request->getParam('id');
        $site = 1;
        $perms = new Jimw_Site_Siteperm();
        $perm = $perms->fetchAll(array('site_id = ?' => $site , 'group_id = ?' => $id));
        if (! count($perm)) {
            // on la cr� !
            $perm = $perms->fetchNew();
            $perm->site_id = $site;
            $perm->group_id = $id;
            $perm->allow = 1;
            $perm->admin = 0;
            $perm->save();
        } else {
            $perm = $perm->current();
        }
        $this->view->perm = $perm;
        $this->view->form_type = 'save';
        $this->view->id = $perm->id;
        $this->render('edit');
    }
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $perms = new Jimw_Site_SitePerm();
        $save = $perms->find($id)->current();
        $save->id = $id;
        $save->allow = $req->allow;
        $save->admin = $req->admin;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index', 'group');
    }
    public function addAction ()
    {
        $req = $this->_request;
        $id = $req->getParam('id');
        $group = $req->getParam('group');
        $perms = new Jimw_Site_Perm();
        $perm = $perms->fetchNew();
        $this->view->perm = $perm;
        $this->view->form_type = 'insert';
        $this->view->id = $id;
        $this->view->group = $group;
        $this->render('add');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        $perm = new Jimw_Site_Perm();
        $save = $perm->fetchNew();
        $save->siteperm_id = $req->id;
        $save->name = $req->name;
        $save->type = 0;
        $save->allow = $req->allow;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('edit', 'perm', 'default', array('id' => $req->group));
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $perms = new Jimw_Site_Perm();
        $perm = $perms->find($id);
        if (! count($perm)) {
            throw new Jimw_Admin_Exception("The site $id doesn't exist");
        }
        $perm = $perm->current();
        if ($perm->type == 1) {
            throw new Jimw_Admin_Exception("You can't delete a system perm");
        }
        $url = $perm->id;
        $perm->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

