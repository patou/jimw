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

include_once('blog/Controller/Model/BlogMessage.php');
include_once('blog/Controller/Model/BlogMessage/Row.php');
include_once('blog/Controller/Model/BlogMessage/Rowset.php');
include_once('blog/Controller/Model/BlogComment.php');
class Categoryblog_CategoryblogController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$parent = $tree->getParam('tree', $tree->id);
		$modules = new Jimw_Site_Module();
		$module = $modules->fetchRow($modules->select()->where('module_path = ?', 'blog'));
		$trees = new Jimw_Site_Tree();
		$parent = $trees->find($parent);
		$blogMessage = new BlogMessage();
		
		$select = $blogMessage->select()->from(array('b' => $blogMessage->getRealTableName()), '*')
		            ->join(array('t' => $trees->getRealTableName()),'t.tree_id = b.tree_parentid', array())
		            ->where('t.tree_status = ?', 4)
		            ->where('t.module_id = ?', $module->id)
		            ->order('b.blogmessage_date DESC')
		            ->limit($tree->getParam('number', 10));
		if (count($parent)) {
		    $parent = $parent->current();
		    $select->where('t.tree_lft >= ?', $parent->lft)
		           ->where('t.tree_rgt <= ? ', $parent->rgt);
		}

		$result = $blogMessage->fetchAll($select);//$trees->fetchAllChildren($parent, 'module_id = 1', array('tree_editiondate DESC', 'tree_lft ASC'), $tree->getParam('number', 5));
		$this->view->messages = $result;
		$this->view->tree = $tree;
		$this->render('blog/blog', null, true);
	}
}
?>