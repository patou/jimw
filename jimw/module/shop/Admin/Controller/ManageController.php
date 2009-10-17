<?php
/**
 * indexController.php
 *
 * @author	   Logisim
 * @category   Blog_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ManageController.php 332 2009-07-19 18:15:39Z patou.de.saint.steban $
 */
include_once (dirname(__FILE__) . '/../../Controller/Model/ShopSong.php');
include_once (dirname(__FILE__) . '/../../Controller/Model/ShopAlbum.php');
class Shop_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
		$this->_forward('list', 'album');
	}

	public function deleteAction () {
		/*$messages = new BlogMessage();
		$list = $messages->fetchAll(array('tree_parentid = ?' => $this->getRequest()->id));
		foreach ($list as $message) {
			$message->delete();
		}*/
	  $this->_forward('list', 'tree', 'default');
	}
}
?>