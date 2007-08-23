<?php
/**
 * Jimw_Module_Action
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 
abstract class Jimw_Module_Action extends Zend_Controller_Action 
{
	public function init() {
		$request = $this->getRequest ();
		$tree = $request->getTree ();
		$this->view->path = $request->getBaseUrl () . '/' . trim($tree['site_path'], '/') . '/template';
		$this->view->path_public = $request->getBaseUrl () . '/' . trim($tree['site_path'], '/');
		$this->getHelper('ViewRenderer')->setViewSuffix($request->getParam('ext', 'phtml'));
		$this->view->addScriptPath(trim($request->site_path, '/') . '/template');
		$this->view->request = $request;
		$this->view->tree = $tree;
		$this->initModule();
	}
	
	/**
	 * Initialisation of the module
	 * Call by init method
	 *
	 */
	
	public function initModule() {
		
	}
	
	abstract public function viewModule ($alias);
	/**
	 * Get the 
	 *
	 * @param unknown_type $id
	 * @param unknown_type $param
	 */
	public function __call ($alias, $param) {
		//Check Alias
		//Get Hierarichy
		
		//Call viewModule function
		$this->viewModule($alias);	
	}
}
?>