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
include_once (dirname(__FILE__) . '/../../Controller/Model/BlogComment.php');
class Blog_MessageController extends Jimw_Admin_Action
{
	public function listAction () {
		$messages = new BlogMessage();
		$this->view->messages_list = $messages->fetchAll(array('tree_parentid = ?' => $this->view->id), 'blogmessage_date DESC');
	}

	public function editAction () {
		$request = $this->getRequest();
		$id = $request->id;
		$this->view->request = $request;
		$this->view->id = $id;
		$messages = new BlogMessage();
		$message = $messages->fetchRow(array('blogmessage_id = ?' => $id));
		if (!$message)
			throw new Jimw_Admin_Exception('Message doesn\'t exist');
		$this->view->message = $message;
		$this->view->form_type = 'save';
		$this->render('form');
	}

	public function saveAction () {
		$request = $this->getRequest();
		$id = $request->id;
		$messages = new BlogMessage();
		if (is_numeric($id)) {
		   $message = $messages->fetchRow(array('blogmessage_id = ?' => $id));
		} else {
		   $message = false;
		}
		if (!$message) {
			$message = $messages->fetchNew();
			$message->tree_parentid = $request->treeparentid;
			$message->header = $request->header;
			$message->title = $request->title;
			$message->content = $request->content;
			$message->date = $request->date;
			$message->tree_id = $request->treeid;
			$message->user_id = 0;
			$message->save();
		} else {
			$message->header = $request->header;
			$message->tree_parentid = $request->treeparentid;
			$message->title = $request->title;
			$message->content = $request->content;
			$message->date = $request->date;
			$message->tree_id = $request->treeid;
			$message->user_id = 0;
			$message->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
		$this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $message->tree_parentid), 'format', true), array('prependBase' => false));
	}

	public function insertAction () {
		$request = $this->getRequest();
		$id = $request->id;
		$messages = new BlogMessage();
		$message = $messages->fetchNew();
		$message->tree_parentid = $request->treeparentid;
		$message->header = $request->header;
		$message->title = $request->title;
		$message->content = $request->content;
		$message->date = $request->date;
		$message->tree_id = $request->treeid;
		$message->comment = true;
		Jimw_Debug::dump($message);
		$message->user_id = 0;
		$message->save();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Insert successful');
		$this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $message->tree_parentid), 'format', true), array('prependBase' => false));
	}

	public function addAction () {
		$messages = new BlogMessage();
		$message = $messages->fetchNew();
		$message->tree_parentid = $this->getRequest()->getParam('id');
		$message->date = @date('Y-m-d H:i:s', time());
		$this->view->message = $message;
		$this->view->form_type = 'insert';
		$this->view->id = '';
		$this->render('form');
	}

	public function deleteAction () {
		$id = $this->getRequest()->id;
		$blogs = new BlogMessage();
		$blog = $blogs->find($id);
		if (!count($blog)) {
			throw new Jimw_Admin_Exception('Message doesn\'t exist');

		}
		$blog = $blog->current();
		$blogid = $blog->tree_parentid;
		$blog->delete ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful');
		$this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $message->tree_parentid), 'format', true), array('prependBase' => false));
	}
}
?>
