<?php
/**
 * Jimw_Admin.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
//define('JIMW_NO_REWRITE_URL', true);
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
if (! defined('JIMW_REP')) {
    if (file_exists('../jimw/'))
        define('JIMW_REP', '../jimw/'); 
    else
        define('JIMW_REP', '../');
}
// Load Global Configuration file
    include (JIMW_REP . 'config/global.php'); 
else {
    /** Module directory */
    define('JIMW_REP_MODULE', JIMW_REP . 'module/');
    /** Lib directory */
    define('JIMW_REP_LIB', JIMW_REP . 'lib/');
}
if (! defined('JIMW_DEBUG_MODE'))
    define('JIMW_DEBUG_MODE', false);
else
    error_reporting(E_ALL | E_STRICT);
    // Autoload initialisation
require_once ('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader' , 'autoload'));
if (JIMW_DEBUG_MODE) {
	Jimw_Debug::initDebug();
}
// Session
$session = new Zend_Session_Namespace('Admin');
// Global configuration
    Zend_Registry::set('config_db', $jimw_config_db); 
else
    Zend_Registry::set('config_db', array('type' => 'PDO_SQLITE' , 'dbname' => JIMW_REP . 'config/config.db'));
    // Call the Global Controler
    $controler = new Jimw_Admin_Controller();
    $controler->run();
    //Zend_Debug::dump($frontcontroller);
    Jimw_Debug::display_exception($e);
}
if (JIMW_DEBUG_MODE) {
	$ext = Zend_Registry::get('ext');
	if (!empty($ext) && $ext == 'phtml') {
		$db = Zend_Registry::get('db');
		Jimw_Debug::profile_db($db, 'Database');
		Jimw_Debug::profile_db(Zend_Registry::get('db_global'), 'Global');
		$totalTime = microtime(true) - $startTime;
		//calculate the time difference
		Jimw_Debug::display("\ntotal execution time: $totalTime .");
	}
}
?>