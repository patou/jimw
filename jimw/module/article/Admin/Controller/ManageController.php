<?php
/**
 * indexController.php
 *
 * @author	    Patou
 * @category   Article_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
include_once (dirname(__FILE__) . '/../../Controller/Model/Article.php');
class Article_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
		$request = $this->getRequest();
		$id = $request->id;
		$article = new Article();
		$result = $article->findByTree($id);
		if (!$result) {
			$new = $article->fetchNew();
			$new->content = '';
			$new->tree_id = $id;
			$new->date = $article->getFormatedDate(new Zend_Date());
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
		$article = new Article();
		$result = $article->findByTree($id);
		if (!$result) {
			$new = $article->fetchNew();
			$new->content = $this->_request->content;
			$new->date = $article->getFormatedDate(new Zend_Date());
			$new->tree_id = $id;
			$new->status = 0;
			$new->revisioninfo = '';
			$new->save();
		} else {
			$result->content = $this->_request->content;
			$result->date = $article->getFormatedDate(new Zend_Date());
			$result->status = 0;
			$result->revisioninfo = '';
			$result->save ();
		}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ');
		$this->_forward('index', 'tree', 'default', array('id' => $id));
	}

	public function deleteAction () {
		$id = $this->getRequest()->id;
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