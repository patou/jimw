<?php

/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
$startTime = microtime(true);
if (get_magic_quotes_runtime() != 0)
    set_magic_quotes_runtime(0);

if (get_magic_quotes_gpc() == 1) {

    function remove_magic_quotes(&$array) {
        foreach ($array as $key => $val) {


            if (is_array($val)) {
                remove_magic_quotes($array[$key]);
            } else if (is_string($val)) {
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
    define('JIMW_ROOT', './');
}
if (!defined('JIMW_REP'))
    if (file_exists(JIMW_ROOT . 'jimw/'))
        define('JIMW_REP', JIMW_ROOT . 'jimw/');
    else
        define('JIMW_REP', JIMW_ROOT);

// Load Global Configuration file
if (file_exists(JIMW_REP . 'config/global.local.php'))
    include(JIMW_REP . 'config/global.local.php');
if (file_exists(JIMW_REP . 'config/global.default.php'))
    include(JIMW_REP . 'config/global.default.php');
else {
    /** JIWM lang */
    if (!defined('JIMW_LANG'))
        define('JIMW_LANG', 'en');
    /** Module directory */
    if (!defined('JIMW_REP_MODULE'))
        define('JIMW_REP_MODULE', JIMW_REP . 'module/');
    /** Lib directory */
    if (!defined('JIMW_REP_LIB'))
        define('JIMW_REP_LIB', JIMW_REP . 'lib/');
    /** Cache directory */
    if (!defined('JIMW_REP_CACHE'))
        define('JIMW_REP_CACHE', './cache/');
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
set_include_path(JIMW_REP_LIB . PATH_SEPARATOR . JIMW_REP_MODULE . PATH_SEPARATOR . JIMW_REP . PATH_SEPARATOR . get_include_path());
try {
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace(array('Jimw_', 'Zym_'));
date_default_timezone_set(JIMW_TIMEZONE);
// Global configuration
if (isset($jimw_config_db))
    Zend_Registry::set('config_db', $jimw_config_db);
else
    Zend_Registry::set('config_db', array('type' => 'PDO_SQLITE', 'dbname' => JIMW_REP . 'config/config.db'));
//Session
Zend_Session::start();
$session = new Zend_Session_Namespace('Jimw');

$cache = Zend_Cache::factory('Core', 'File', array('automatic_serialization' => true), array('cache_dir' => JIMW_REP_CACHE . 'jimw'));
Zend_Locale::setCache($cache);

if (!JIMW_DEBUG) {
    $cache = Zend_Cache::factory('Core', 'File', array('automatic_serialization' => true), array('cache_dir' => JIMW_REP_CACHE . 'db'));
    Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
// geting a Zend_Cache_Frontend_Page object
    $cache = Zend_Cache::factory('Page', 'File', array('lifetime' => 7200), array('cache_dir' => JIMW_REP_CACHE . 'html'));
    $_GET['jimw.cache.page.hostname'] = $_SERVER['HTTP_HOST']; // For work with many host
    $cache->start();
    Zend_Registry::set('cache', $cache);
}
Jimw_Debug::initDebug(JIMW_DEBUG);
// Call the Global Controler
    $controler = new Jimw_Global_Controller();
    $controler->run();
} catch (Exception $e) {
    Jimw_Debug::display_exception($e);
}

//if (JIMW_DEBUG_MODE) {
$ext = (Zend_Registry::isRegistered('format')) ? Zend_Registry::get('format') : 'phtml';
if (empty($ext) || $ext != 'phtml') {
    Jimw_Debug::disactive();
}
$db = Zend_Registry::get('db');
Jimw_Debug::display($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' (' . $_SERVER['HTTP_USER_AGENT'] . ')');
Jimw_Debug::profile_db($db, 'Database');
Jimw_Debug::profile_db(Zend_Registry::get('db_global'), 'Global');
$totalTime = microtime(true) - $startTime;
//calculate the time difference
Jimw_Debug::display("\ntotal execution time: $totalTime .");
//}
