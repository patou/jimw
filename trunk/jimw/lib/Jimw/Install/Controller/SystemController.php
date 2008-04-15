<?php
/**
 * SystemController.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: IndexController.php 100 2007-10-25 18:44:08Z patou.de.saint.steban $
 */
class SystemController extends Jimw_Install_Action 
{
    public function indexAction() {
        
    }
	public function phpinfoAction () {
	    ob_start();
	    phpinfo();
	    $this->view->phpinfo = ob_get_clean();
	}

	public function permissionAction() {
	    $dir_write = array('jimw' => false, 'jimw/lib' => false, 'cache' => true);
	    $directory = array();
	    foreach ($dir_write as $dir => $write) {
	        if (is_dir(JIMW_ROOT . $dir)) {
	            $directory[$dir] = array(is_writable(JIMW_ROOT . $dir) && $write, $write);
	        }
	    }
	    $this->view->directory = $directory;
	}
	
	public function configurationAction () {
	    $const_default = array(
	        'JIMW_DEBUG_MODE' => false,
	        'JIMW_DEFAULT_EXT' => 'phtml',
	        'JIMW_URL_REWRITING' => true,
	        'JIMW_URL_MODULE_PATH' => 'module',
	        'JIMW_URL_DEFAULT_PATH' => 'default',
	        'JIMW_TIMEZONE' => 'Europe/Paris',
	        'JIMW_LANG' => 'fr'
	    );
	    $const = array();
	    foreach ($const_default as $name => $default) {
	        if (defined($name)) {
	            $value = constant($name);
	            $const[$name] = array(($default == $value), $value, $default);
	        }
	    }
	    $this->view->configuration = $const;
	}
}

?>