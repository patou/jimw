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
class Blog_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
		$id = $this->_request->id;
		$this->view->request = $this->_request;
		$blog = new Blog();
		$result = $news->findByTree($this->view->id);
		if (!$result) {
			$new = $blog->fetchNew();
			$new->content = '';
			$new->tree_id = $id;
			$new->date = '1900-01-01 00:00:00';
			$new->status = 0;
			$new->revisioninfo = '';
			$new->save();
			$result = $new;
		}
		$this->view->content = $result->content;
		$this->view->id = $id;
	}

	public function saveAction () {
		$id = $this->_request->id;
		$news = new News();
		$result = $news->findByTree($id);
		if (!$result) {
			$new = $news->fetchNew();
			$new->content = $this->_request->content;
			$new->date = '1900-01-01 00:00:00';
			$new->tree_id = $id;
			$new->status = 0;
			$new->revisioninfo = '';
			$new->save();
		} else {
			$result->content = $this->_request->content;
			$result->date = '1900-01-01 00:00:00';
			$result->status = 0;
			$result->revisioninfo = '';
			$result->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ');
		$this->_forward('index', 'tree', 'default', array('id' => $id));
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