<?php
/**
 * ProtectController.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: IndexController.php 100 2007-10-25 18:44:08Z patou.de.saint.steban $
 */
class ProtectController extends Jimw_Install_Action 
{
	public function indexAction () {
	    $session = new Zend_Session_Namespace('Install');
	    $session->protect_file = JIMW_INSTALL_PROTECTPREFIX . md5(time());
	    $this->view->protect_file = $session->protect_file;
		$this->render('index');
	}
}
?>