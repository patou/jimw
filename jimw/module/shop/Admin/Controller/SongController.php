<?php
/**
 * indexController.php
 *
 * @author	   Logisim
 * @category   Blog_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: CommentController.php 264 2009-02-28 12:25:19Z patou.de.saint.steban $
 */
include_once (dirname(__FILE__) . '/../../Controller/Model/BlogMessage.php');
include_once (dirname(__FILE__) . '/../../Controller/Model/BlogComment.php');
include_once (dirname(__FILE__) . '/../../Controller/Model/BlogCommentForm.php');
class Blog_CommentController extends Jimw_Admin_Action
{
	public function listAction () {
		$messages = new BlogMessage();
		$request = $this->getRequest();
		$id = $request->blogmessage_id;
		$message = $messages->find($id);
		if (!count($message))
			throw new Jimw_Admin_Exception('Message didn\'t exist');
		$this->view->message = $message->current();
		$comments = new BlogComment();
		$result = Zend_Paginator::factory($comments->select()->where('blogmessage_id = ?', $id));

		// initialisation des valeurs par défaut
		$result->setPageRange(10);
		$result->setItemCountPerPage(10);
		$page = 1;
		if (isset($request->page))
		    $page = $request->page;
		$result->setCurrentPageNumber($page);

		$this->view->comments_list = $result;
	}

	public function editAction () {
	    $request = $this->getRequest();
		$id = $request->blogcomment_id;
		$comments = new BlogComment();
		$comment = $comments->fetchRow(array('blogcomment_id = ?' => $id));
		if (!$comment) {
            throw new Jimw_Admin_Exception('This comment didn\'t exist');
		}
		$form = new BlogCommentForm();
		if ($request->isPost() && $form->isValid($request->getPost())) {
		    $values = $form->getValues();
            $comment->username = $values['blogcomment_username'];
			$comment->content = $values['blogcomment_content'];
			$comment->date = $value['blogcomment_date'];
			$comment->save();
			$this->_helper->getHelper('FlashMessenger')->addMessage ('Save Comment successful');
			$this->_forward('list', 'comment', 'blog', array('blogmessage_id' => $comment->blogmessage_id));
		}
		else {
		    $form->populate($comment->toArray());
		    $this->view->form = $form;
		    $this->view->comment = $comment;
		}
	}

	public function saveAction () {
		$id = $this->_request->id;
		$comments = new BlogComment();
		if (is_numeric($id)) {
		   $comment = $comments->fetchRow(array('blogcomment_id = ?' => $id));
		} else {
		   $comment = false;
		}
		if (!$comment) {
			$comment = $comments->fetchNew();
			$comment->username = $this->_request->username;
			$comment->content = $this->_request->content;
			$comment->date = $this->_request->date;
			$comment->user_id = 0;
			$comment->save();
		} else {
			$comment->username = $this->_request->username;
			$comment->content = $this->_request->content;
			$comment->date = $this->_request->date;
			$comment->user_id = 0;
			$comment->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save Comment successful');
		$this->_forward('list', 'comment', 'blog', array('id' => $comment->blogmessage_id));
	}

	public function deleteAction () {
		$id = $this->_request->id;
		$comments = new BlogComment();
		$comment = $comments->find($id);
		if (!count($comment)) {
			throw new Jimw_Admin_Exception('Comment didn\'t exists');
		}
		$comment = $comment->current();
		$blogmessage = $comment->blogmessage_id;
		$comment->delete();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete Comment successful ');
		$this->_forward('list', 'comment', 'default', array('blogmessage_id' => $blogmessage));
	}
}
?>