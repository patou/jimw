<?php
/**
 * ManageController.php
 *
 * @author	    Patou
 * @category   Gallery_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Gallerylist_ManageController extends Jimw_Admin_Action
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
		$this->view->directory = $tree->getParam('dir');
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
		$tree->setParam ('dir', trim($this->_request->directory, '/'));
		$tree->save();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ');
		$this->_forward ('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>