<?php
/**
 * indexController.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: IndexController.php 100 2007-10-25 18:44:08Z patou.de.saint.steban $
 */
class IndexController extends Jimw_Install_Action 
{
	public function indexAction () {
		if (!file_exists(JIMW_REP_CONFIG . '/global.local.php')) {
		    $this->_forward('index', 'install');
		}
	}
}
?>