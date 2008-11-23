<?php
/**
 * ShopController
 *
 * @author	   Logisim
 * @category   Jimw_Shop
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: $
 */

include_once('Model/ShopAlbum.php');
include_once('Model/ShopSong.php');
class Shop_ShopController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->getRequest();
		$tree = $request->getTree();
		
		// Current album
		$albummodel = new ShopAlbum();
		$albums = $albummodel->findByTree($tree->id);
		$this->view->albums = $albums;
		
		$this->view->tree = $tree;
		$this->render('album');
	}
	
}
?>