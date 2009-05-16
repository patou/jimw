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
include_once (dirname(__FILE__) . '/../Form/MessageForm.php');
class Blog_MessageController extends Jimw_Admin_Action
{
	public function listAction () {
		$messages = new BlogMessage();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$result = Zend_Paginator::factory($messages->select()->where('tree_parentid = ?', $id)->order('blogmessage_date DESC'));

		// initialisation des valeurs par défaut
		$result->setPageRange(10);
		$result->setItemCountPerPage(10); //TODO $this->module->config->nbPerPage
		$page = 1;
		if (isset($request->page))
		    $page = $request->page;
		$result->setCurrentPageNumber($page);

		$this->view->messages_list = $result;
		$this->view->tree_parentid = $id;
	}

	public function editAction () {
		$request = $this->getRequest();
		$id = $request->blogmessage_id;
		$form = new MessageForm();
		$form->addSubmit('Save');
		$messages = new BlogMessage();
		$message = $messages->fetchRow(array('blogmessage_id = ?' => $id));
		if (!$message)
			throw new Jimw_Admin_Exception('Message doesn\'t exist');
		if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $message->header = $values['blogmessage_header'];
			$message->title = $values['blogmessage_title'];
			$message->content = $values['blogmessage_content'];
			$message->date = $values['blogmessage_date'];
			$message->comment = $values['blogmessage_comment'];
			$message->tree_id = $values['tree_id'];
			$message->save ();
			$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
			$this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $message->tree_parentid), 'format', true), array('prependBase' => false));
		}
		else {
		    if (!$request->isPost()) {
		        $form->populate($message->toArray());
		    }
		    $this->view->form = $form;
		    $this->render('form');
		}
	}
/**
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
	}*/

	public function addAction () {
	    $request = $this->getRequest();
		$id = $request->message_id;
		$form = new MessageForm();
		$form->addSubmit('Save');
		$messages = new BlogMessage();
		$message = $messages->fetchNew();
		$message->tree_parentid = $request->getParam('tree_parentid');
		$message->date = $message->getFormatedDate(Zend_Date::now());
		if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $message->header = $values['blogmessage_header'];
			$message->title = $values['blogmessage_title'];
			$message->content = $values['blogmessage_content'];
			$message->comment = $values['blogmessage_comment'];
			$message->date = $values['blogmessage_date'];
			$message->tree_id = $values['tree_id'];
			$message->save ();
			$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
			$this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $message->tree_parentid), 'format', true), array('prependBase' => false));
		}
		else {
		    if (!$request->isPost()) {
		        $message->comment = 1;
		        $form->populate($message->toArray());
		    }
		    $this->view->form = $form;
		    $this->render('form');
		}
	}

	public function deleteAction () {
		$id = $this->getRequest()->blogmessage_id;
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
