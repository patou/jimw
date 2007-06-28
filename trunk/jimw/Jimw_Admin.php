<?php
/**
 * Jimw_Admin.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:  $
 */
if (!defined('JIMW_REP'))
	if (file_exists('../jimw/'))
		define('JIMW_REP', '../jimw/');
	else
		define('JIMW_REP', '../');
// Load Global Configuration file
if (file_exists(JIMW_REP. 'config/global.php'))
	include(JIMW_REP. 'config/global.php');
else {
	/** Module directory */
	define('JIMW_REP_MODULE', JIMW_REP . 'module/');
	/** Lib directory */
	define('JIMW_REP_LIB', JIMW_REP . 'lib/');
}
if (!defined('JIMW_DEBUG_MODE'))
	define('JIMW_DEBUG_MODE', false);
else
	error_reporting(E_ALL|E_STRICT);
// Autoload initialisation
set_include_path(JIMW_REP_LIB . PATH_SEPARATOR . JIMW_REP . PATH_SEPARATOR . get_include_path());
require_once('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader', 'autoload'));
// Global configuration
if (isset ($jimw_config_db))
	Zend_Registry::set('config_db', $jimw_config_db);
else
	Zend_Registry::set('config_db', array('type' => 'PDO_SQLITE', 'dbname' => JIMW_REP . 'config/config.db'));
	
// Call the Global Controler
try {
	$frontcontroller = Zend_Controller_Front::getInstance();
	$request = new Jimw_Global_Request();
	$router = new Jimw_Global_Router();
	$request = $router->route($request);
	$frontcontroller->setRequest($request);
	$frontcontroller->throwExceptions(true);
	$frontcontroller->setControllerDirectory(JIMW_REP_LIB . 'Jimw/Admin/Controller');
	$frontcontroller->setModuleControllerDirectoryName('Admin/Controller');
	$frontcontroller->addModuleDirectory(JIMW_REP_MODULE);
	$frontcontroller->registerPlugin(new Jimw_Admin_GlobalRender ());
	$frontcontroller->dispatch();
	//Zend_Debug::dump($frontcontroller);
	//Zend_Debug::dump($controler->getRequest(), 'Request : ');
}
// Display exceptions
catch (Zend_Exception $e) {
	if (JIMW_DEBUG_MODE)
		echo $e->getMessage();
	else
		echo 'error'; /** @todo Change it to more friendly error*/
}
catch (Exception $e) {
	if (JIMW_DEBUG_MODE)
		echo $e->getMessage();
	else
		echo 'error'; /** @todo Change it to more friendly error*/
}
?>