<?php
/**
 * GalleryController
 *
 * @author	    Patou
 * @category   Jimw_Gallery
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */


class Gallery_GalleryController extends Jimw_Module_Action
{
	public function viewModule ($alias)
	{

		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$tree = $request->getTree();
		/* @var $tree Jimw_Site_Tree_Row */
		$photos = array();
		$rep = trim($tree->getParam('dir'), '/');
		$dir = rtrim('./' . $tree->site->path, '/') . '/' . $rep;
		$base_path = $this->view->path_public . '/' . $rep . '/';
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($img = readdir($dh)) !== false) {
					if ($img != '.' && $img != '..' && !is_dir($dir . '/' . $img)) {
						if (($size = @getimagesize($dir . '/thumbnails/' . $img)) !== false) {
							$photos[] = array ('url' => $base_path . $img, 'title' => '', 'thumbnails' => $base_path . 'thumbnails/' . $img, 'thumbnails_width' => $size[0], 'thumbnails_height' => $size[1], 'file' => $img);
						}
					}
				}
				closedir($dh);
			}
		}
		else
			exit ("$dir isn't a valid directory");
		$this->view->photos = $photos;
		$this->view->photos_path = $dir;
		$this->render('gallery', 'body');
	}
}
