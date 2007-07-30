<?php
/**
 * Jimw_Admin_Action
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 
abstract class Jimw_Admin_Action extends Zend_Controller_Action 
{
	public function init() {
		$request = $this->getRequest ();
		$tree = $request->getTree ();
		$this->getHelper('ViewRenderer')->setViewSuffix($request->getParam('ext', 'ajax'));
		$this->view->request = $request;
		$this->view->path = $request->getBaseUrl() . '/public';
		$this->view->tree = $tree;
		$this->initModule();
	}
	
	public function initModule () {
		
	}
}