<?php
/**
 * GallerylistController
 *
 * @author	   Logisim
 * @category   Jimw_Gallerylist
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */


class Gallerylist_GallerylistController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{

		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$currenttree = $request->getTree();
		//$parent = $tree->id;
		$trees = new Jimw_Site_Tree();
		$result = $trees->fetchAllChildren($currenttree->id);
		
		/* @var $tree Jimw_Site_Tree_Row */
		//$tree = new Jimw_Site_Tree_Row();
		$galleries = array();
		foreach($result as $tree) {
			$added = false;
			$site = $tree->findParentJimw_Site_Site();
    		$rep = trim($tree->getParam('dir'), '/');
    		$dir = rtrim('./' . $site->path, '/') . '/' . $rep;
    		$base_path = $this->view->path_public . '/' . $rep . '/';
    		if (is_dir($dir)) {
    			if ($dh = opendir($dir)) {
    				while (($img = readdir($dh)) !== false && !$added) {
    					if ($img != '.' && $img != '..' && !is_dir($dir . '/' . $img)) {
    						if (($size = @getimagesize($dir . '/thumbnails/' . $img)) !== false) {
    						   	//$url = $this->view->url(array('alias' => $tree->alias, 'format' => 'phtml'), 'alias', true);
    							$galleries[] = array ('alias' => $tree->alias, 'menutitle' => $tree->menutitle, 'thumbnails' => $base_path . 'thumbnails/' . $img, 'thumbnails_width' => $size[0], 'thumbnails_height' => $size[1]);
    							$added = true;
    						}
    					}
    				}
    				closedir($dh);
    			}
    		}
    		else
    			exit ("$dir isn't a valid directory");
    		
    	}
		$this->view->galleries = $galleries;
		//$this->view->photos_path = $dir;
		$this->render('gallerylist');
	}
}
