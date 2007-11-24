<?php
/**
 * DomainController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ModuleController.php 116 2007-11-22 16:28:30Z patou.de.saint.steban $
 */

class DomainController extends Zend_Controller_Action
{
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
        $this->_forward('list');
    }
    
    public function listAction()
    {
    	$domains = new Jimw_Site_Domain();
    }
}
		
