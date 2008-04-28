<?php
/**
 * ManageController.php
 *
 * @author	    Patou
 * @category   Links_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ManageController.php 131 2007-12-06 00:08:13Z patou.de.saint.steban $
 */
class Categoryblog_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
		$id = $this->_request->id;
		$treeFactory = new Jimw_Site_Tree();
		$result = $treeFactory->find($id);
		if (!count($result)) {
			throw new Jimw_Admin_Exception();
		}
		$tree = $result->current();
		/* @var $tree Jimw_Site_Tree_Row */
        $this->view->tree_root =  $tree->getParam('tree', 0);
        $this->view->number =  $tree->getParam('number', 5);
        $this->view->id = $id;
	}

	public function saveAction () {
		$id = $this->_request->id;
		$treeFactory = new Jimw_Site_Tree();
		$result = $treeFactory->find($id);
		if (!count($result)) {
			throw new Jimw_Admin_Exception();
		}
		$tree = $result->current();
		/* @var $tree Jimw_Site_Tree_Row */
		$tree->setParam ('tree', $this->_request->tree_root);
		$tree->setParam ('number', $this->_request->number);
		$tree->save();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
		$this->_forward('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>