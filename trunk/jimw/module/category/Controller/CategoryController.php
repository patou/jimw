<?php
/**
 * CategoryController
 *
 * @author	    Patou
 * @category   Jimw_Category
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Category_CategoryController extends Jimw_Module_Action
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$parent = $tree->getParam('tree', $tree->id); 
		$trees = new Jimw_Site_Tree();
		$result = $trees->fetchAllChildren($parent, 'module_id = 1', array('tree_editiondate DESC', 'tree_lft ASC'), $tree->getParam('number', 5));
		$this->view->list_article = $result;
		$this->view->tree = $tree;
		$this->render('category', 'body');
	}
}
?>