<?php
define('JIMW_REP', '../jimw/');

include(JIMW_REP. 'config/global.php');

define('JIMW_DEBUG_MODE', true);
// Autoload initialisation
set_include_path(JIMW_REP_LIB . PATH_SEPARATOR . JIMW_REP . PATH_SEPARATOR . get_include_path());
require_once('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader', 'autoload'));
require_once('./configuration.php');
if (isset($_GET['test'])) {
	echo '<h1>', $_GET['test'] , '</h1>';
	include('./'.str_replace('_', '/', $_GET['test']).'Test.php');
}