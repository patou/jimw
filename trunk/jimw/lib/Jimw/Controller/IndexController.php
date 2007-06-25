<?php
/**
 * IndexController
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 
class IndexController extends Zend_Controller_Action 
{
	public function index () {
		//$this->getResponse()->appendBody('Index');
		$this->_forward('toto', 'article', 'article');
	}
}
?>