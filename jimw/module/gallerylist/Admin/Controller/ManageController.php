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
		
	}

	public function saveAction () {
		$this->_forward ('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>