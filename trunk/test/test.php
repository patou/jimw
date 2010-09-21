<?php
define('JIMW_REP', '../jimw/');

include(JIMW_REP. 'config/global.php');

define('JIMW_DEBUG', true);
// Autoload initialisation
set_include_path(JIMW_REP_LIB . PATH_SEPARATOR . JIMW_REP . PATH_SEPARATOR . get_include_path());
require_once('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader', 'autoload'));
require_once('./configuration.php');
if (isset($_GET['test'])) {
	echo '<h1>', $_GET['test'] , '</h1>';
	include('./'.str_replace('_', '/', $_GET['test']).'Test.php');
}
else {
	function parc_dir($dir) {
		$d = opendir($dir);
		while (($file = readdir($d)) !== false) {
			if ($file[0] == '.')
				continue;
			if (is_dir($dir.'/'.$file)) {
				parc_dir($dir.'/'.$file);
			}
			elseif ('Test.php' == substr($file, strlen($file) - 8)) {
				$test = trim(str_replace(array('/', '.'), array('_', ''), $dir).'_'.substr($file, 0, strlen($file) - 8), '_');
				echo '<a href="test.php?test=',$test,'">', $test, '</a><br />';
			}
		}
	}
	parc_dir('.');
}