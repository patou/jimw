<?php
/**
 * ManageController.php
 *
 * @author	   Logisim
 * @category   Iframe_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2010 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: $
 */
class Iframe_ManageController extends Jimw_Admin_Action
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
		$this->view->redirect_url =  $tree->getParam('url');
		$this->view->visited =  $tree->getParam('count', 0);
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
		$tree->setParam ('url', trim($this->_request->redirect_url, '/'));
		$tree->save();
		$this->_helper->getHelper('FlashMessenger')->addMessage('Save successful');
		$this->_forward('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>