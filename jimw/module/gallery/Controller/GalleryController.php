<?php
/**
 * ArticleController
 *
 * @author	    Patou
 * @category   Jimw_Article
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
		$photos = array();
		$dir = rtrim('./' . $tree['site_path'], '/') . '/' . trim($tree['tree_param'], '/');
		$base_path = $this->view->path_public . '/' . trim($tree['tree_param'], '/') . '/';
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($img = readdir($dh)) !== false) {
					if ($img != '.' && $img != '..' && !is_dir($dir . '/' . $img)) {
						$size = getimagesize($dir . '/thumbnails/' . $img);
						$photos[] = array ('url' => $base_path . $img, 'title' => '', 'thumbnails' => $base_path . 'thumbnails/' . $img, 'thumbnails_width' => $size[0], 'thumbnails_height' => $size[1]);
					}
				}
				closedir($dh);
			}
		}
		else
			exit ("$dir isn't a valid directory");
		$this->view->photos = $photos;
		$this->render('gallery', 'body');
	}
}
?>