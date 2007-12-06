<?php
/**
 * indexController.php
 *
 * @author	   Patou
 * @category   ModuleController
 * @package    Jimw_Admin
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */



class SystemController extends Jimw_Admin_Action
{
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
        // TODO Auto-generated SystemController::indexAction() default action    
    }

	public function updateAction() {
		$this->view->content = file_get_contents('http://' .$this->_request->get("SERVER_NAME") . '/install/update.php');
	}
	
	public function updatetreeAction() {
		$this->view->content = file_get_contents('http://' .$this->_request->get("SERVER_NAME") . '/install/update_tree.php');
		$this->render('update');
	}
}
		
