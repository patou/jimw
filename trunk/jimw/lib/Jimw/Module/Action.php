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
	public function indexAction () {
		$this->getResponse()->appendBody('Article');
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