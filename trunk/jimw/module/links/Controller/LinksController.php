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


class Links_LinksController extends Jimw_Module_Action
{
	public function viewModule ($alias)
	{

		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$tree = $request->getTree();
		$this->_redirect($tree['tree_param']);
	}
}
