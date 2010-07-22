<?php
/**
 * LinksController
 * Module who redirect a page to an other webpage
 * @author	    Patou
 * @category   Jimw_Links
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: LinksController.php 316 2009-06-26 15:17:14Z patou.de.saint.steban $
 */


class Iframe_IframeController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$treeFactory = new Jimw_Site_Tree();
		$tree = $this->_request->getTree();
		$tree = $treeFactory->find($tree->id)->current();
		$tree->param->count += 1;
		$tree->save();
		$this->view->url = $tree->param->url;
		$this->view->height = $tree->param->height;
		$this->view->width = $tree->param->width;
		$this->render('iframe');
	}
}
