<?php
/**
 * indexController.php
 *
 * @author	   Logisim
 * @category   Blog_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
include_once (dirname(__FILE__) . '/../../Controller/Model/BlogMessage.php');
class Blog_MessageController extends Jimw_Admin_Action
{
	public function listAction () {
		$messages = new BlogMessage();
		$this->view->messages_list = $messages->fetchAll(array('tree_parentid = ?' => $this->view->id), 'blogmessage_date DESC');
	}
	public function editAction () {
		$id = $this->_request->id;
		$this->view->request = $this->_request;
		$this->view->id = $id;
		$message = new BlogMessage();
		$result = $message->fetchRow(array('blogmessage_id = ?' => $id));
		$this->view->message = $result;
		/*
		if (!$result) {
			$new = $message->fetchNew();
			$new->content = '';
			$new->tree_parentid = $id;
			$new->date = date('Y-m-d H:i:s', time());
			$new->status = 0;
			$new->revisioninfo = '';
			$new->save();
			$result = $new;
		}
		$this->view->content = $result->content;
		$this->view->id = $id;
		*/
		
	}

	public function saveAction () {
		$id = $this->_request->id;
		$messages = new BlogMessage();
		if (is_numeric($id)) {
		   $result = $messages->fetchRow(array('blogmessage_id = ?' => $id));
		} else {
		   $result = false;
		}
		if (!$result) {
			$new = $messages->fetchNew();
			$new->tree_parentid = $this->_request->treeparentid;
			$new->header = '';
			$new->title = $this->_request->title;
			$new->content = $this->_request->content;
			$new->date = $this->_request->date;
			$new->tree_id = $this->_request->treeid;
			$new->user_id = 0;
			$new->save();
		} else {
			$result->header = '';
			$result->tree_parentid = $this->_request->treeparentid;
			$result->title = $this->_request->title;
			$result->content = $this->_request->content;
			$result->date = $this->_request->date;
			$result->tree_id = $this->_request->treeid;
			$result->user_id = 0;
			$result->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ');
		$this->_forward('index', 'tree', 'default', array('id' => $id));
	}

	public function addAction () {
		$messages = new BlogMessage();
		$result = $messages->fetchNew();
		$result->tree_parentid = $this->_request->getParam('id');
		$this->view->message = $result;
		$this->view->id = '';
		//$this->render('form');
	}

	public function deleteAction () {
		$id = $this->view->id;
		$article = new Article();
		$result = $article->findByTree($id);
		if ($result) {
			$result->delete ();
			$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ');
		} else {
			throw new Jimw_Admin_Exception();
		}
		$this->_forward('index', 'tree', 'default');
	}
}
?>