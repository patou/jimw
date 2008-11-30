<?php
/**
 * GroupController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class GroupController extends Jimw_Admin_Action
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
        $groups = new Jimw_Site_Group();
        $this->view->groups_list = $groups->fetchAll();
    }
    public function editAction ()
    {
        $id = $this->_request->getParam('id');
        $groups = new Jimw_Site_Group();
        $group = $sites->find($id);
        if (! count($group)) {
            Jimw_Debug::dump($group);
            throw new Jimw_Admin_Exception("The group $id doesn't exist");
        }
        $group = $group->current();
        if ($group->type == 1) {
            throw new Jimw_Admin_Exception("You can't edit a system group");
        }
        $this->view->group = $group;
        $this->view->form_type = 'save';
        $this->view->id = $id;
        $this->render('form');
    }
    public function addAction ()
    {
        $req = $this->_request;
        $groups = new Jimw_Site_Group();
        $group = $groups->fetchNew();
        $this->view->group = $group;
        $this->view->form_type = 'insert';
        $this->view->id = '';
        $this->render('form');
    }
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $groups = new Jimw_Site_Group();
        $save = $groups->find($id)->current();
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
        $group = new Jimw_Site_Group();
        $save = $group->fetchNew();
        $save->name = $req->name;
        $save->type = 0;
        $save->parents = $req->parents;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $groups = new Jimw_Site_Group();
        $group = $groups->find($id);
        if (! count($group)) {
            throw new Jimw_Admin_Exception("The site $id doesn't exist");
        }
        $group = $group->current();
        if ($group->type == 1) {
            throw new Jimw_Admin_Exception("You can't delete a system group");
        }
        $url = $group->id;
        $group->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

