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
class Blog_CommentController extends Jimw_Admin_Action
{
	public function listAction () {
		$messages = new BlogMessage();
		$message = $messages->find($this->_request->id);
		if (!$message->exists())
			throw new Jimw_Admin_Exception('Message didn\'t exist');
		$this->view->message = $message->current();
		$comments = new BlogComment();
		$this->view->comments_list = $comments->fetchAll(array('blogmessage_id = ?' => $this->_request->id));
	}
	
	public function editAction () {
		$id = $this->_request->id;
		$this->view->request = $this->_request;
		$this->view->id = $id;
		$comments = new BlogComment();
		$comment = $comments->fetchRow(array('blogcomment_id = ?' => $id));
		$this->view->comment = $comment;
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
		if (!$comment->exists()) {
			throw new Jimw_Admin_Exception('Comment didn\'t exists');
		}
		$comment = $comment->current();
		$blogmessage = $comment->blogmessage_id;
		$comment->delete();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete Comment successful ');
		$this->_forward('list', 'comment', 'default', array('id' => $blogmessage));
	}
}
?>