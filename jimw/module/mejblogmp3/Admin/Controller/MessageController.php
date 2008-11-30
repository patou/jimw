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
include_once (dirname(__FILE__) . '/../../Controller/Model/Mejblogmp3Message.php');
include_once (dirname(__FILE__) . '/../../Controller/Model/Mejblogmp3Comment.php');
class Mejblogmp3_MessageController extends Jimw_Admin_Action
{
	public function listAction () {
		$messages = new Mejblogmp3Message();
		$this->view->messages_list = $messages->fetchAll(array('tree_parentid = ?' => $this->view->id), 'blogmp3message_date DESC');
	}

	public function editAction () {
		$id = $this->_request->id;
		$this->view->request = $this->_request;
		$this->view->id = $id;
		$messages = new Mejblogmp3Message();
		$message = $messages->fetchRow(array('blogmp3message_id = ?' => $id));
		if (!$message)
			throw new Jimw_Admin_Exception('Message doesn\'t exist');
		$this->view->message = $message;
		$this->view->form_type = 'save';
		$this->render('form');
	}

	public function publishAction () {
		$id = $this->_request->id;
		$messages = new Mejblogmp3Message();
		if (is_numeric($id)) {
		   $message = $messages->fetchRow(array('blogmp3message_id = ?' => $id));
		} else {
		   $message = false;
		}
		if ($message) {
			$message->published = true;
			$message->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Publish successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $message->tree_parentid));
	}

	public function unpublishAction () {
		$id = $this->_request->id;
		$messages = new Mejblogmp3Message();
		if (is_numeric($id)) {
		   $message = $messages->fetchRow(array('blogmp3message_id = ?' => $id));
		} else {
		   $message = false;
		}
		if ($message) {
			$message->published = false;
			$message->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Publish successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $message->tree_parentid));
	}

	public function saveAction () {
		$id = $this->_request->id;
		$messages = new Mejblogmp3Message();
		if (is_numeric($id)) {
		   $message = $messages->fetchRow(array('blogmp3message_id = ?' => $id));
		} else {
		   $message = false;
		}
		if (!$message) {
			$message = $messages->fetchNew();
			$message->tree_parentid = $this->_request->treeparentid;
			$message->header = '';
			$message->title = $this->_request->title;
			$message->content = $this->_request->content;
			$message->date = $this->_request->date;
			$message->tree_id = $this->_request->treeid;
			$message->user_id = 0;
			$message->save();
		} else {
			$message->header = '';
			$message->tree_parentid = $this->_request->treeparentid;
			$message->title = $this->_request->title;
			$message->content = $this->_request->content;
			$message->date = $this->_request->date;
			$message->tree_id = $this->_request->treeid;
			$message->user_id = 0;
			$message->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $message->tree_parentid));
	}

	public function insertAction () {
		$id = $this->_request->id;
		$messages = new Mejblogmp3Message();
		$message = $messages->fetchNew();
		$message->tree_parentid = $this->_request->treeparentid;
		$message->header = '';
		$message->title = $this->_request->title;
		$message->content = $this->_request->content;
		$message->date = $this->_request->date;
		$message->tree_id = $this->_request->treeid;
		$message->comment = true;
		Jimw_Debug::dump($message);
		$message->user_id = 0;
		$message->save();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Insert successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $message->tree_parentid));
	}

	public function addAction () {
		$messages = new Mejblogmp3Message();
		$message = $messages->fetchNew();
		$message->tree_parentid = $this->_request->getParam('id');
		$message->date = @date('Y-m-d H:i:s', time());
		$this->view->message = $message;
		$this->view->form_type = 'insert';
		$this->view->id = '';
		$this->render('form');
	}

	public function deleteAction () {
		$id = $this->_request->id;
		$blogs = new Mejblogmp3Message();
		$blog = $blogs->find($id);
		if (!count($blog)) {
			throw new Jimw_Admin_Exception('Message doesn\' exist');

		}
		$blog = $blog->current();
		$blogid = $blog->tree_parentid;
		$blog->delete ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $blogid));
	}
}
?>