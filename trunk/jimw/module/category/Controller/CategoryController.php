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

class Category_CategoryController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$parent = $tree->getParam('tree_root', $tree->id);
		$trees = new Jimw_Site_Tree();
		$result = $trees->fetchAllChildren($parent, array('module_path = ?' => 'article', 'tree_parentid = ?' => $parent), array('tree_lft DESC'), $tree->getParam('number', 5));
		$this->view->list_article = $result;
		$this->view->tree = $tree;
		$this->render('category');
	}
}
?>