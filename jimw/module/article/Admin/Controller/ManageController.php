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
class Article_ManageController extends Zend_Controller_Action 
{
	public function editAction () {
		$this->view->id = $this->_request->id;
		$this->view->path = $this->_request->getBaseUrl() . '/public';
        $this->view->request = $this->_request;
        		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
        $select = $db->select();
		$select->from('jimw_article', 'article_content');
		$select->where('tree_id = ?', $this->view->id);
        $this->view->content = $db->fetchOne($select);
	}
	
	public function saveAction () {
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$db->update('jimw_article', array('article_content' => stripslashes($this->_request->content)), $db->quoteInto('tree_id = ?', $this->_request->id));

		$this->_forward('edit', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>