<?php
/**
 * treeController.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:  $
 */
class TreeController extends Zend_Controller_Action 
{
	public function editAction () {
		$id = $this->_request->getParam('id');
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', array('id' => 'tree_id', 'pagetitle'=> 'tree_pagetitle', 'menutitle'=> 'tree_menutitle', 'alias'=> 'tree_alias', 'description'=> 'tree_description', 'order' => 'tree_order'));
		$select->where('tree_id = ?', $id);
		$result = $db->fetchRow($select);
		$this->view->tree = $result;
		$this->view->path = $this->_request->getBaseUrl() . '/public';
        $this->view->request = $this->_request;
        $this->view->form_type = 'save';
        $this->view->id = $id;
        $this->render('form');
	}
	
	public function addAction () {
		$this->view->tree = array('id' => '',
					'pagetitle'=> '',
					'menutitle'=> '',
					'alias'=> '',
					'description'=> '',
					'order' => 0);
		$this->view->path = $this->_request->getBaseUrl() . '/public';
        $this->view->request = $this->_request;
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
		$this->_forward('index', 'index');		
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
		/* @var $db Zend_Db_Adapter_Abstract */
		$db->update('jimw_tree', $save, $db->quoteInto('tree_id = ?', $req->id));
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ' . $req->pagetitle);
		$this->_forward('index', 'index');
	}
	
	
	public function insertAction () {
		$req = $this->_request;
		$save = array('tree_pagetitle'=> $req->pagetitle, 
				'tree_menutitle'=> $req->menutitle,
				'tree_alias'=> $req->alias,
				'tree_description'=> $req->description,
				'tree_order'=> $req->order,
				'site_id' => 1,
				'module_id' => 1);
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$db->insert('jimw_tree', $save);
		$id = $db->lastInsertId();
		//Create the article
		$article = array ('tree_id' => $id,
							'article_content' => '');
		$db->insert('jimw_article', $article);
		//End Create article
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Insert successful ' . $req->pagetitle);
		$this->_forward('index', 'index');
	}
}
?>