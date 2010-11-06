<?php
/**
 * Jimw_Install.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Jimw_Admin.php 183 2008-03-13 16:17:31Z logisim $
 */
$startTime = microtime(true);
if (get_magic_quotes_runtime() != 0)
	set_magic_quotes_runtime(0);

if (get_magic_quotes_gpc() == 1){
	function remove_magic_quotes(&$array)
	{
		foreach($array as $key => $val){


			if(is_array($val)){
				remove_magic_quotes($array[$key]);
			} else if(is_string($val)){
				$array[$key] = stripslashes($val);
			}
		}
	}

	remove_magic_quotes($_POST);
	remove_magic_quotes($_GET);
	remove_magic_quotes($_REQUEST);
	remove_magic_quotes($_SERVER);
	remove_magic_quotes($_FILES);
	remove_magic_quotes($_COOKIE);
}
if (!defined('JIMW_ROOT')) {
	define ('JIMW_ROOT', '../');
}
if (!defined('JIMW_ADMIN_ROOT')) {
	define ('JIMW_ADMIN_ROOT', '../admin');
}
if (! defined('JIMW_REP')) {
    if (file_exists('../jimw/'))
        define('JIMW_REP', '../jimw/');
    else
        define('JIMW_REP', '../');
}
// Load Global Configuration file
if (file_exists(JIMW_REP . 'config/global.local.php'))
    include (JIMW_REP . 'config/global.local.php');
if (file_exists(JIMW_REP . 'config/global.default.php'))
    include (JIMW_REP . 'config/global.default.php');
else {
	/** JIWM lang */
	if (!defined('JIMW_LANG')) define('JIMW_LANG', 'en');
	/** Module directory */
	if (!defined('JIMW_REP_MODULE')) define('JIMW_REP_MODULE', JIMW_REP . 'module/');
	/** Lib directory */
	if (!defined('JIMW_REP_LIB')) define('JIMW_REP_LIB', JIMW_REP . 'lib/');
}
if (defined('JIMW_DEBUG_KEY') && !empty($_GET['_debug_']) && $_GET['_debug_'] == JIMW_DEBUG_KEY) {
    define('JIMW_DEBUG', true);
} elseif (!defined('JIMW_DEBUG_MODE')) {
    define('JIMW_DEBUG', false);
}
else {
    define('JIMW_DEBUG', JIMW_DEBUG_MODE);
}
if (JIMW_DEBUG) {
    error_reporting(E_ALL | E_STRICT);
}
// Autoload initialisation
set_include_path(JIMW_REP_LIB . PATH_SEPARATOR . JIMW_REP . PATH_SEPARATOR . get_include_path());
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace(array('Jimw_', 'Zym_'));
if (JIMW_DEBUG) {
	Jimw_Debug::initDebug();
}
// Session
Zend_Session::start();
$session = new Zend_Session_Namespace('Install');
// Global configuration
/*if (isset($jimw_config_db))
    Zend_Registry::set('config_db', $jimw_config_db);
else
    Zend_Registry::set('config_db', array('type' => 'PDO_SQLITE' , 'dbname' => JIMW_REP . 'config/config.db'));
*/
    // Call the Global Controler
try {
    $controler = new Jimw_Install_Controller();
    $controler->run();
    //Zend_Debug::dump($frontcontroller);
//Zend_Debug::dump($controler->getRequest(), 'Request : ');
} // Display exceptions
catch (Exception $e) {
    Jimw_Debug::display_exception($e);
}
if (JIMW_DEBUG) {
	$ext = (Zend_Registry::isRegistered('format')) ? Zend_Registry::get('format') : 'phtml';
	if (!empty($ext) && $ext == 'phtml') {
	    if (Zend_Registry::isRegistered('db')) {
    		$db = Zend_Registry::get('db');
    		Jimw_Debug::profile_db($db, 'Database');
	    }
	    if (Zend_Registry::isRegistered('db_global')) {
		    Jimw_Debug::profile_db(Zend_Registry::get('db_global'), 'Global');
	    }
		$totalTime = microtime(true) - $startTime;
		//calculate the time difference
		Jimw_Debug::display("\ntotal execution time: $totalTime .");
	}
    else {
        Jimw_Debug::disactive();
	}
}
?>