<?php
/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:  $
 */
if (!defined('JIMW_REP'))
	if (file_exists('./jimw/'))
		define('JIMW_REP', './jimw/');
	else
		define('JIMW_REP', './');
// Load Global Configuration file
if (file_exists(JIMW_REP. 'config/global.php'))
	include(JIMW_REP. 'config/global.php');
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
	$controler = new Jimw_Global_Controller();
	$controler->run();
	Zend_Debug::dump($controler->getRequest(), 'Request : ');
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