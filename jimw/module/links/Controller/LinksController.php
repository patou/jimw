<?php
/**
 * LinksController
 * Module who redirect a page to an other webpage
 * @author	    Patou
 * @category   Jimw_Links
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */


class Links_LinksController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$treeFactory = new Jimw_Site_Tree();
		$tree = $this->_request->getTree();
		$tree = $treeFactory->find($tree->id)->current();
		$tree->param->count += 1;
		$tree->save ();
		$cache = Zend_Registry::get('cache');
		if ($cache != null )
		    $cache->cancel();
		$this->_redirect($tree->param->url);
	}
}
