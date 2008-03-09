<?php
/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Jimw.php 92 2007-09-19 21:41:39Z patou.de.saint.steban $
 */
$startTime = microtime(true);
define('JIMW_REP', '../jimw/');
// Configuration
define('DEFAULT_VERSION', 1);
// End Configuration
if (file_exists(JIMW_REP. 'config/global.php'))
include(JIMW_REP. 'config/global.php');
else {
	/** Module directory */
	define('JIMW_REP_MODULE', JIMW_REP . 'module/');
	/** Lib directory */
	define('JIMW_REP_LIB', JIMW_REP . 'lib/');
	/** Cache directory */
	define('JIMW_CACHE_LIB', './cache/');
}
if (!defined('JIMW_DEBUG_MODE')) {
	define('JIMW_DEBUG_MODE', false);
}
else {
	error_reporting(E_ALL|E_STRICT);
}

set_include_path(JIMW_REP_LIB . PATH_SEPARATOR . JIMW_REP . PATH_SEPARATOR . get_include_path());
require_once('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader', 'autoload'));

function display_exception(Exception $e) {
	echo "<font color=red>Exception [", get_class($e), '-', $e->getCode(), ']';
	echo $e->getMessage(), "<br /></font>";
	if (JIMW_DEBUG_MODE) {
		echo 'Error in ', $e->getFile(), ' at line ', $e->getLine(), "\n<br /><ol>";
		$stack = $e->getTrace();
		//Zend_Debug::dump($stack);
		foreach ($stack as $item) {
			echo '<li>', $item['file'], ' (', $item['line'], ') in ', (isset($item['class']) ? $item['class']. $item['type'] : ''), $item['function'],'()</li>';
		}
		echo '</ol>';
	}
}

function get_schema_version ($db, $default = 'global', $prefix = 'jimw_') {
	$version[$default] = DEFAULT_VERSION;
	try {
		$schemas = $db->fetchAll($db->select ()->from($prefix . 'schema', '*'));
		foreach ($schemas as $schema) {
			$version[$schema['schema_module']] = $schema['schema_version'];
		}
	}
	catch (Zend_Db_Exception $e) {
		//$version[$default] = DEFAULT_VERSION;
		display_exception($e);
	}
	return $version;
}

function get_database_type($type) {
	switch (strtoupper($type)) {
		case 'PDO_MYSQL':
		case 'MYSQLI':
		case 'MYSQL':
			return 'mysql';
		case 'PDO_SQLITE':
		case 'SQLITE':
			return 'sqlite';
		default:
			return '';
	}
}

function get_version_list($module, $path) {
	$list = array();
	try {
		$dir = new DirectoryIterator ($path);
		
		foreach ($dir as $file) {
			if (!$dir->isDot()) {
				if (ereg('^([0-9]{3})-'.$module.'.sql$', $file->getFilename (), $result)) {
					$list[intval($result[0])] = $path . $file->getFilename ();
				}
			}
		}
	}
	catch (Exception $e) {
		display_exception($e);
	}
	return $list;
}

function install_sql($file, Zend_Db_Adapter_Abstract $db, $prefix) {
	$sql_file = @file_get_contents($file);
	if (!$sql_file) {
		echo "<font color=red>KO</font><br />";
		return false;
	}
	$all_sql = explode(";", $sql_file);
	foreach ($all_sql as $sql) {
		$sql = str_replace('{PREFIX}', $prefix, trim($sql));
		if (!empty($sql)) {
			try {
				if (JIMW_DEBUG_MODE)
					echo "SQL : <pre>", $sql, "</pre><br />";
				$stm = $db->query($sql);
				//$stm->execute();
			}
			catch (Zend_Db_Exception $e) {
				display_exception($e);
				return false;
			}
		}
	}
	return true;
}

function update_version($module, $version, Zend_Db_Adapter_Abstract $db, $prefix) {
	
	try {
		$sel = $db->fetchOne($db->select()->from($prefix . 'schema', 'schema_version')->where('schema_module = ?', $module));
		
		echo "** Update $module to version $version **";
		if ($sel)
			$db->update($prefix . 'schema', array('schema_version' => $version), $db->quoteInto('schema_module = ?', $module));
		else
			$db->insert($prefix . 'schema', array('schema_version' => $version, 'schema_module' => $module));
		return true;
	}
	catch (Zend_Db_Exception $e) {
		display_exception($e);
		return false;
	}
}

function install_version ($module, $path, $cur_version = 0, Zend_Db_Adapter_Abstract $db, $prefix = 'jimw_') {
	$list = get_version_list($module, $path);
	$version = $cur_version;
	for (; isset($list[$version]); $version++) {
		echo "Install Version $version ... ";
		if (install_sql ($list[$version], $db, $prefix))
			echo "<font color=green>OK</font><br />";
		else
			return false;
	}
	return update_version($module, $version, $db, $prefix);
}
if (JIMW_DEBUG_MODE) {
    		$jimw_config_db['profiler'] = true;
    		$debug = '';
}
// Connect to the global database :
$db_global = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);
$version = get_schema_version($db_global);
$dir = './sql/' . get_database_type($jimw_config_db['type']) . '/';
echo "<br />--- Update global database ---<br />";
install_version('global', $dir, $version['global'], $db_global);
echo "<br />--- End Update global database ---<br />";

echo "<br />--- Update All databases ---<br />";
$select = $db_global->select();
$select->from('jimw_database');
$databases = $db_global->fetchAll($select);
foreach ($databases as $database) {
	echo "<br />-- Update database {$database['database_id']}--<br />";
	$options = array('dbname' => $database['database_name'],
					'host' => $database['database_server'],
					'port' => $database['database_port'],
					'username' => $database['database_user'],
					'password' => $database['database_pass']);
	if (JIMW_DEBUG_MODE) {
    		$options['profiler'] = true;
    }
	$db = Zend_Db::factory($database['database_type'], $options);
	$prefix = $database['database_prefix'] . '_';
	$local_version = get_schema_version($db, 'database', $prefix);
	$local_dir = './sql/' . get_database_type($database['database_type']) . '/';
	install_version('database', $local_dir, $local_version['database'], $db, $prefix);
	$select = $db->select();
	$select->from($prefix . 'module');
	$modules = $db->fetchAll($select);
	foreach ($modules as $module) {
		$module_name = $module['module_path'];
		$module_dir = JIMW_REP_MODULE . '/' . $module_name . '/install/sql/' . get_database_type($database['database_type']) . '/';
		if (file_exists($module_dir)) {
			echo "<br />-- Update database for module {$module_name}--<br />";
			$module_version = (isset($local_version[$module_name])) ? $local_version[$module_name] : DEFAULT_VERSION;				
			install_version($module_name, $module_dir, $module_version, $db, $prefix);
			echo "<br />-- End Update database for module {$module_name}--<br />";
		}
	}
	
	echo "<br />-- End Update database {$database['database_id']}--";
	if (JIMW_DEBUG_MODE) {
		$debug .= "<hr />Database {$database['database_id']} : <br />";
		if ($db) {
			$profiler = $db->getProfiler ();
			$totalTime    = $profiler->getTotalElapsedSecs();
			$queryCount   = $profiler->getTotalNumQueries();
			$longestTime  = 0;
			$longestQuery = null;
	
			foreach ($profiler->getQueryProfiles() as $query) {
				if ($query->getElapsedSecs() > $longestTime) {
					$longestTime  = $query->getElapsedSecs();
					$longestQuery = $query->getQuery();
				}
				$debug .= 'Query : ' . $query->getElapsedSecs() . ' for ' . $query->getQuery() . "<br />";
			}
	
			$debug .= 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "<br />";
			$debug .= 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br />";
			$debug .= 'Queries per second: ' . $queryCount / $totalTime . "<br />";
			$debug .= 'Longest query length: ' . $longestTime . "<br />";
			$debug .= "Longest query: \n" . $longestQuery . "<br />";
		}
	}
}
echo "<br />--- End Update All databases ---<br />";
if (JIMW_DEBUG_MODE) {
	echo "<hr />Debug Mode : <br />", $debug, "<br /> Global Database : <br />";
	if ($db_global) {
		$profiler = $db_global->getProfiler ();
		$totalTime    = $profiler->getTotalElapsedSecs();
		$queryCount   = $profiler->getTotalNumQueries();
		$longestTime  = 0;
		$longestQuery = null;

		foreach ($profiler->getQueryProfiles() as $query) {
			if ($query->getElapsedSecs() > $longestTime) {
				$longestTime  = $query->getElapsedSecs();
				$longestQuery = $query->getQuery();
			}
			echo 'Query : ', $query->getElapsedSecs(), ' for ', $query->getQuery(), "<br />";
		}

		echo 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "<br />";
		echo 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br />";
		echo 'Queries per second: ' . $queryCount / $totalTime . "<br />";
		echo 'Longest query length: ' . $longestTime . "<br />";
		echo "Longest query: \n" . $longestQuery . "<br />";
	}
	$totalTime = microtime(true) - $startTime;
	//calculate the time difference
	echo "<br />total execution time: $totalTime . <br />";
}