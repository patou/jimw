<?php
/**
 * indexController.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class IndexController extends Zend_Controller_Action 
{
	public function indexAction () {
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', '*');
		$select->order('tree_order ASC');
		$result = $db->fetchAll($select);
		$this->view->list_tree = $result;
		$this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages ();
		$this->view->path = $this->_request->getBaseUrl() . '/public';
        $this->view->request = $this->_request;
	}
}
?>