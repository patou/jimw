<?php
/**
 * CategoryblogController
 *
 * @author	    Patou
 * @category   Jimw_Category
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: CategoryController.php 149 2008-01-13 20:53:49Z patou.de.saint.steban $
 */

include_once(dirname(__FILE__) . '/../../blog/Controller/Model/BlogMessage.php');
include_once(dirname(__FILE__) . '/../../blog/Controller/Model/BlogMessage/Row.php');
include_once(dirname(__FILE__) . '/../../blog/Controller/Model/BlogComment.php');
include_once(dirname(__FILE__) . '/../../blog/Controller/Model/BlogCommentForm.php');
class Categoryblog_CategoryblogController extends Jimw_Module_Action_Alias
{
	/* (non-PHPdoc)
	 * @see Jimw_Module_Action_Alias#viewModule($alias)
	 */
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$parent = $tree->getParam('tree', $tree->id);
		$trees = new Jimw_Site_Tree();
		$parent = $trees->find($parent);
		$blogMessage = new BlogMessage();

		$select = $blogMessage->select()->from(array('b' => $blogMessage->getRealTableName()), '*')
		            ->join(array('t' => $trees->getRealTableName()),'t.tree_id = b.tree_parentid', array())
		            ->where('t.tree_status = ?', Jimw_Site_Tree::PUBLISHED)
		            ->where('t.module_path = ?', 'blog')
		            ->order('b.blogmessage_date DESC')
		            ->limit($tree->getParam('number', 10));
		if (count($parent)) {
		    $parent = $parent->current();
		    $select->where('t.tree_lft >= ?', $parent->lft)
		           ->where('t.tree_rgt <= ? ', $parent->rgt);
		}

		$result = Zend_Paginator::factory($select);

		// initialisation des valeurs par défaut
		$result->setPageRange(10);
		if (isset($tree->param->nbMessageByPage))
		    $result->setItemCountPerPage($tree->param->nbMessageByPage);
		else
		    $result->setItemCountPerPage(10);
		$page = 1;
		if (isset($request->page))
		    $page = $request->page;
		$result->setCurrentPageNumber($page);

		$this->view->messages = $result;
		$form = new BlogCommentForm();
		$form->setAction($this->view->url(array('module' => 'blog', 'controller' => 'comment', 'action' => 'add', 'format' => 'phtml', 'alias' => $tree->alias), 'format'));
		$this->view->form = $form;
		$this->view->tree = $tree;
		$this->render('blog/blog', null, true);
	}
}
?>