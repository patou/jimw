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
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', '*');
		$select->order('tree_order ASC');
		$result = $db->fetchAll($select);
		$this->view->list_tree = $result;
		$this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages ();
	}

	public function editAction () {
		$id = $this->_request->getParam('id');
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', array('id' => 'tree_id', 'pagetitle'=> 'tree_pagetitle', 'menutitle'=> 'tree_menutitle', 'alias'=> 'tree_alias', 'description'=> 'tree_description', 'order' => 'tree_order'));
		$select->joinNatural('jimw_module', array('module' => 'module_path'));
		$select->where('tree_id = ?', $id);
		$result = $db->fetchRow($select);
		$this->view->tree = $result;
		$this->view->form_type = 'save';
		$this->view->id = $id;
		$this->getHelper('ViewRenderer')->noRenderLayout();
		$this->render('form');
		$this->_forward('edit', 'manage', $result['module']);
	}

	public function addAction () {
		$this->view->tree = array('id' => '',
		'pagetitle'=> '',
		'menutitle'=> '',
		'alias'=> '',
		'description'=> '',
		'order' => 0,
		'module' => 0);
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_module', array('id' => 'module_id', 'name'=> 'module_name'));
		$this->view->modules = $db->fetchAll($select);
		$this->view->form_type = 'insert';
		$this->view->id = '';
		$this->render('form');
	}

	public function deleteAction () {
		$req = $this->_request;
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$db->delete('jimw_tree', $db->quoteInto('tree_id = ?', $req->id));
		$db->delete('jimw_article', $db->quoteInto('tree_id = ?', $req->id));
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful '. $req->id);
		$this->_forward('index');
	}

	public function saveAction () {
		$req = $this->_request;
		$save = array('tree_id' => $req->id,
		'tree_pagetitle'=> $req->pagetitle,
		'tree_menutitle'=> $req->menutitle,
		'tree_order'=> $req->order,
		'tree_alias'=> $req->alias,
		'tree_description'=> $req->description);
		$db = Zend_Registry::get('db');
		$tree = new Jimw_Site_Tree();
		/* @var $db Zend_Db_Adapter_Abstract */
		$tree->update($save, $db->quoteInto('tree_id = ?', $req->id));
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ' . $req->pagetitle);
		$this->_forward('index');
	}


	public function insertAction () {
		$req = $this->_request;
		$save = array('tree_pagetitle'=> $req->pagetitle,
		'tree_menutitle'=> $req->menutitle,
		'tree_alias'=> $req->alias,
		'tree_description'=> $req->description,
		'tree_order'=> $req->order,
		'site_id' => 1,
		'module_id' => $req->module);
		$db = Zend_Registry::get('db');
		$tree = new Jimw_Site_Tree();
		/* @var $db Zend_Db_Adapter_Abstract */
		$tree->insert($save);
		$id = $db->lastInsertId();
		//Create the article
		$article = array ('tree_id' => $id,
		'article_content' => '');
		$db->insert('jimw_article', $article);
		//End Create article
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Insert successful ' . $req->pagetitle);
		$this->_forward('edit', 'tree', 'default', array('id'=>$id));
	}
}
?>