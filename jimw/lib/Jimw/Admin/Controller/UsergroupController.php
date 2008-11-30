<?php
/**
 * UsergroupController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class UsergroupController extends Jimw_Admin_Action
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
        $usergroups = new Jimw_Site_Usergroup();
        $this->view->usergroups_list = $usergroups->fetchAll();
    }
    public function editAction ()
    {
        $id = $this->_request->getParam('id');
        $usergroups = new Jimw_Site_Usergroup();
        $usergroup = $sites->find($id);
        if (! count($usergroup)) {
            Jimw_Debug::dump($usergroup);
            throw new Jimw_Admin_Exception("The usergroup $id doesn't exist");
        }
        $usergroup = $usergroup->current();
        $this->view->usergroup = $usergroup;
        $this->view->form_type = 'save';
        $this->view->id = $id;
        $this->render('form');
    }
    public function addAction ()
    {
        $req = $this->_request;
        $usergroups = new Jimw_Site_Usergroup();
        $usergroup = $usergroups->fetchNew();
        $this->view->site = $usergroup;
        $this->view->form_type = 'insert';
        $this->view->id = '';
        $this->render('form');
    }
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $usergroups = new Jimw_Site_Usergroup();
        $save = $usergroups->find($id)->current();
        $save->id = $id;
        $save->name = $req->name;
        $save->parents = $req->parents;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        $usergroup = new Jimw_Site_Usergroup();
        $save = $usergroup->fetchNew();
        $save->name = $req->name;
        $save->parents = $req->parents;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $usergroups = new Jimw_Site_Usergroup();
        $usergroup = $usergroups->find($id);
        if (! count($usergroup)) {
            throw new Jimw_Admin_Exception("The usergroup $id doesn't exist");
        }
        $usergroup = $usergroup->current();
        $url = $usergroup->id;
        $usergroup->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

