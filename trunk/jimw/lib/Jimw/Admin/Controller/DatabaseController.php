<?php
/**
 * DatabaseController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class DatabaseController extends Jimw_Admin_Action
{
    public function preDispatch()
    {
        $this->checkRoleAllowed('admin_system');
    }
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $this->_forward('list');
    }
    public function listAction ()
    {
        $databases = new Jimw_Global_Database();
        $this->view->databases_list = $databases->fetchAll();
    }
    public function editAction ()
    {
        $id = $this->_request->getParam('id');
        $databases = new Jimw_Global_Database();
        $result = $databases->find($id);
        if (! count($result)) {
            Jimw_Debug::dump($result);
            throw new Jimw_Admin_Exception("The database $id doesn't exist");
        }
        $result = $result->current();
        $this->view->database = $result;
        $this->view->form_type = 'save';
        $this->view->id = $id;
        $this->render('form');
    }
    public function addAction ()
    {
        $req = $this->_request;
        $databases = new Jimw_Global_Database();
        $database = $databases->fetchNew();
        $database->type = 'PDO_MYSQL';
        $database->prefix = 'jimw';
        $this->view->database = $database;
        $this->view->form_type = 'insert';
        $this->view->id = '';
        $this->render('form');
    }
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $tree = new Jimw_Global_Database();
        $save = $tree->find($id)->current();
        $save->id = $id;
        $save->type = $req->type;
        $save->server = $req->server;
        $save->name = $req->name;
        $save->prefix = $req->prefix;
        $save->user = $req->user;
        $save->pass = $req->pass;
        $save->port = $req->port;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        $tree = new Jimw_Global_Database();
        $save = $tree->fetchNew();
        $save->type = $req->type;
        $save->server = $req->server;
        $save->name = $req->name;
        $save->prefix = $req->prefix;
        $save->user = $req->user;
        $save->pass = $req->pass;
        $save->port = $req->port;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $databases = new Jimw_Global_Database();
        $database = $databases->find($id);
        if (! count($database)) {
            throw new Jimw_Admin_Exception("The database $id doesn't exist");
        }
        $database = $database->current();
        $url = $database->id;
        $database->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

