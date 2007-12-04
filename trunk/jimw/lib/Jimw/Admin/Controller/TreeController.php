<?php
/**
 * treeController.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class TreeController extends Jimw_Admin_Action
{
	public function indexAction () {
		if ($this->_helper->getHelper('FlashMessenger')->hasMessages())
			$this->_forward('list');
		else
			$this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages ();
	}

	public function listAction () {
		$tree = new Jimw_Site_Tree();
		$tree->updateAllChildren(101, array('tree_editiondate' => new Zend_Db_Expr('NOW()'), 'tree_creationdate' => new Zend_Db_Expr('NOW()')), 'module_id = 3');
		$tree->fetchAllChildren(101, null, 'tree_alias DESC');
		$session = new Zend_Session_Namespace('Admin');
		if (isset($session->site) && isset($session->site->tree_id))
			$parentid = $tree->find($session->site->tree_id)->current();
		else
			$parentid = null;
		$this->view->list_tree = $tree->getChildren($parentid ? $parentid->parentid : 0);
		$this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages ();
	}

	public function editAction () {
		$id = $this->_request->getParam('id');
		$tree = new Jimw_Site_Tree();
		$result = $tree->find($id);
		if (!$result->exists()) {
			Zend_Debug::dump($result);
			throw new Jimw_Admin_Exception("The tree $id didn't exist");
		}
		$result = $result->current();
		$this->view->tree = $result;
		$this->view->form_type = 'save';
		$this->view->id = $id;
		$this->getHelper('ViewRenderer')->noRenderLayout();
		$this->render('form');
		$this->_forward('edit', 'manage', $result->findParentJimw_Site_Module()->path);
	}

	public function addAction () {
		$tree = new Jimw_Site_Tree();
		$this->view->tree = $tree->fetchNew();
		$module = new Jimw_Site_Module();
		$this->view->modules = $module->fetchAll();
		$this->view->form_type = 'insert';
		$this->view->id = '';
		$this->render('form');
	}

	public function deleteAction () {
		$id = $this->_request->id;
		$tree = new Jimw_Site_Tree();
		$result = $tree->find($id);
		if (!$result->exists()) {
			throw new Jimw_Admin_Exception("The tree $id didn't exist");
		}
		$result = $result->current();
		$module_path = $result->findParentJimw_Site_Module()->path;
		$result->delete ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful '. $id);
		$this->_forward('delete', 'manage', $module_path);
	}

	public function saveAction () {
		$req = $this->_request;
		$id = $req->id;
		$tree = new Jimw_Site_Tree();
		$save = $tree->find($id)->current();
		$save->id = $id;
		$save->pagetitle = $req->pagetitle;
		$save->menutitle = $req->menutitle;
		$save->order = $req->order;
		$save->parentid = $req->parentid;
		$save->alias = $req->alias;
		$save->description = $req->description;
		$save->save ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ' . $req->pagetitle);
		$this->_forward('index');
	}


	public function insertAction () {
		$req = $this->_request;
		//Zend_Debug::dump($req);
		$tree = new Jimw_Site_Tree();
		$save = $tree->fetchNew();
		$save->pagetitle = $req->pagetitle;
		$save->menutitle = $req->menutitle;
		$save->order = $req->order;
		$save->parentid = $req->parentid;
		$save->module_id = $req->getPost('module');
		$save->site_id = 1;
		$save->version = 0;
		$save->status = 0;
		$save->param = '';
		$save->icon = '';
		$save->image = '';
		//$save->creationdate = '1900-01-01 00:00:00';
		//$save->editiondate = '1900-01-01 00:00:00';
		$save->alias = $req->alias;
		$save->description = $req->description;
		//Zend_Debug::dump($save);
		$save->save ();
		$id = $save->id;
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Insert successful ' . $req->pagetitle);
		$this->_forward('edit', 'tree', 'default', array('id'=>$id));
		//$this->_forward('index');
	}
	
	public function moveAction () {
		
		$tree = new Jimw_Site_Tree();
		$save = $tree->find($this->_request->id)->current();
		$save->order += ($this->_request->direction == 'up') ? -1 : 1;
		// Todo: chercher si un noeux à le même order et switcher
		$save->save ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Move successful ' . $save->pagetitle); 
		$this->_forward('index');
	}
}
?>