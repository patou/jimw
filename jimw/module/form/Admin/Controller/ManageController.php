<?php
/**
 * ManageController.php
 *
 * @author	    Patou
 * @category   Links_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ManageController.php 92 2007-09-19 21:41:39Z patou.de.saint.steban $
 */
class Form_ManageController extends Jimw_Admin_Action
{
	public function editAction () {		
	    $this->view->form = '';
	}

	public function saveAction () {
		$this->_forward('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>