<?php
/**
 * DomainController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ModuleController.php 116 2007-11-22 16:28:30Z patou.de.saint.steban $
 */
class DomainController extends Jimw_Admin_Action
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
    /**
     * List all domain of the current site
     *
     */
    public function listAction ()
    {
        $domains = new Jimw_Site_Domain();
        $database = Zend_Registry::get('database');
        $site = Zend_Registry::get('site');
        $this->view->domains_list = $domains->fetchAll($domains->select()->where('database_id = ?', $database->id)->where('site_id = ?', $site->id));
    }
    /**
     * Add a new domain
     *
     */
    
    public function addAction ()
    {
        $req = $this->getRequest();
        $domainTable = new Jimw_Site_Domain();
        $form = new Jimw_Admin_Form_DomainForm();
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $values = $form->getValues();
            $save = $domainTable->createFromUrl($values['domain']);
            $site = Zend_Registry::get('site');
	        $database = Zend_Registry::get('database');
	        $save->site_id = $site->id;
	        $save->database_id = $database->id;
	        $save->status = 0;
	        if ($values['domain_catch_domain']) {
	            $save->subdomain = '*';
	        }
	        if ($values['domain_catch_all']) {
	            $save->name = '*';
	        }
	        $save->save();
            $this->_helper->getHelper('FlashMessenger')->addMessage('Add successful domain');
            $this->_forward('list');
        } else {
            $form->addSubmit('Add');
            $this->view->form = $form;
            $this->render('form');
        }
    }
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $tree = new Jimw_Site_Domain();
        $save = $tree->find($id)->current();
        $save->id = $id;
        $save->name = $req->name;
        $save->subdomain = $req->subdomain;
        $save->protocol = $req->protocol;
        $save->path = $req->path;
        $save->port = $req->port;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->__toString());
        $this->_forward('index');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        $tree = new Jimw_Site_Domain();
        $site = Zend_Registry::get('site');
        $database = Zend_Registry::get('database');
        $save = $tree->fetchNew();
        $save->site_id = $site->id;
        $save->database_id = $database->id;
        $save->status = 0;
        $save->name = $req->name;
        $save->subdomain = $req->subdomain;
        $save->protocol = $req->protocol;
        $save->path = $req->path;
        $save->port = $req->port;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->__toString());
        $this->_forward('index');
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $domains = new Jimw_Site_Domain();
        $domain = $domains->find($id);
        if (! count($domain)) {
            throw new Jimw_Admin_Exception("The domain $id doesn't exist");
        }
        $domain = $domain->current();
        $url = $domain->__toString();
        $domain->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

