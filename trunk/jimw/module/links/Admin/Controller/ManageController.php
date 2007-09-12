<?php
/**
 * ManageController.php
 *
 * @author	    Patou
 * @category   Links_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Links_ManageController extends Jimw_Admin_Action 
{
	public function editAction () {
		$this->view->id = $this->_request->id;
		$this->view->request = $this->_request;
        		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
        $select = $db->select();
		$select->from('jimw_tree', array ('tree_param', 'tree_version'));
		$select->where('tree_id = ?', $this->view->id);
		$row = $db->fetchRow($select);
        $this->view->redirect_url = $row['tree_param'];
        $this->view->visited = $row['tree_version'];
	}
	
	public function saveAction () {
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$db->update('jimw_tree', array('tree_param' => trim($this->_request->redirect_url, '/')), $db->quoteInto('tree_id = ?', $this->_request->id));
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful ');
		$this->_forward('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>