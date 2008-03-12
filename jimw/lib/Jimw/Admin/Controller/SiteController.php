<?php
/**
 * SiteController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class SiteController extends Jimw_Admin_Action
{
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
    	$this->_forward('list');
    }
    
    public function listAction()
    {
    	$sites = new Jimw_Site_Site();
    	$this->view->sites_list = $sites->fetchAll();
    }
    
	public function editAction () {
		$id = $this->_request->getParam('id');
		$sites = new Jimw_Site_Site();
		$result = $sites->find($id);
		if (!$result->exists()) {
			Jimw_Debug::dump($result);
			throw new Jimw_Admin_Exception("The site $id doesn't exist");
		}
		$result = $result->current();
		$this->view->site = $result;
		$this->view->form_type = 'save';
		$this->view->id = $id;
		$this->render('form');
	}
	
	public function addAction () {
		$req = $this->_request;
		$sites = new Jimw_Site_Site();
		$site = $sites->fetchNew();
		$site->tree_id = 1;
		$site->access = 0;
		$this->view->site = $site;
		$this->view->form_type = 'insert';
		$this->view->id = '';
		$this->render('form');
	}
	
	public function saveAction () {
		$req = $this->_request;
		$id = $req->id;
		$tree = new Jimw_Site_Site();
		$save = $tree->find($id)->current();
		$save->id = $id;
		$save->tree_id = $req->tree_id;
		$save->access = $req->access;
		$save->name = $req->name;
		$save->path = $req->path;
		$save->save ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ' . $save->id);
		$this->_forward('index');
	}
	
	public function insertAction () {
		$req = $this->_request;
		$tree = new Jimw_Site_Site();
		$save = $tree->fetchNew();
		$save->tree_id = $req->tree_id;
		$save->access = $req->access;
		$save->name = $req->name;
		$save->path = $req->path;
		$save->save ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ' . $save->id);
		$this->_forward('index');
	}
	
	public function deleteAction () {
		$id = $this->_request->id;
		$sites = new Jimw_Site_Site();
		$site = $sites->find($id);
		if (!$site->exists()) {
			throw new Jimw_Admin_Exception("The site $id doesn't exist");
		}
		$site = $site->current();
		$url = $site->id;
		$site->delete ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful '. $url);
		$this->_forward('list');
	}
}
		
