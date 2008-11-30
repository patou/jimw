<?php
/**
 * UserController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class UserController extends Jimw_Admin_Action
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
        $users = new Jimw_Site_User();
        $this->view->users_list = $users->fetchAll();
    }
    public function editAction ()
    {
        $id = $this->_request->getParam('id');
        $users = new Jimw_Site_User();
        $user = $users->find($id);
        if (! count($user)) {
            Jimw_Debug::dump($user);
            throw new Jimw_Admin_Exception("The user $id doesn't exist");
        }
        $user = $user->current();
        $this->view->user = $user;
        $this->view->form_type = 'save';
        $this->view->id = $id;
        $this->render('form');
    }
    public function addAction ()
    {
        $req = $this->_request;
        $users = new Jimw_Site_User();
        $user = $users->fetchNew();
        $this->view->user = $user;
        $this->view->form_type = 'insert';
        $this->view->id = '';
        $this->render('form');
    }
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $users = new Jimw_Site_User();
        $save = $users->find($id)->current();
        $save->login = $req->login;
        if (! empty($req->password))
            $save->password = md5($req->password);
        $save->firstname = $req->firstname;
        $save->lastname = $req->lastname;
        $save->status = 0;
        $save->email = $req->email;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->login);
        $this->_forward('index');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        $user = new Jimw_Site_User();
        $save = $user->fetchNew();
        $save->login = $req->login;
        $save->password = md5($req->password);
        $save->firstname = $req->firstname;
        $save->lastname = $req->lastname;
        $save->status = 0;
        $save->email = $req->email;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->login);
        $this->_forward('index');
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $users = new Jimw_Site_User();
        $user = $users->find($id);
        if (! count($user)) {
            throw new Jimw_Admin_Exception("The site $id doesn't exist");
        }
        $user = $user->current();
        $url = $user->login;
        $user->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

