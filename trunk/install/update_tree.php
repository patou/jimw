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

function list_tree(Zend_Db_Adapter_Abstract $db, $prefix) {
	$select = $db->select();
	$select->from($prefix . 'tree');
	$select->order(array('tree_order', 'tree_alias'));
	$result = $db->fetchAll($select);
	if ($result === false) {
		return false;
	} else {
		$list = array();
		foreach ($result as $node) {
			$list[$node['tree_parentid']][] = $node;
		}
		return $list;
	}
}

function reorder_tree($parent, $order) {
	global $db, $list, $prefix;
	foreach ($list[$parent] as $tree) {
		echo 'tree = ' . $tree['tree_alias'] . '<br />' . "\n";
		echo 'lft = ' . $order . '<br />' . "\n";
		$lft = $order++;
		if (isset($list[$tree['tree_id']]))
			$order = reorder_tree ($tree['tree_id'], $order);
		echo 'rgt = ' . $order . '<br />' . "\n";
		$rgt = $order++;
		$db->update($prefix . 'tree', array('tree_lft' => $lft, 'tree_rgt' => $rgt), 'tree_id = ' . $tree['tree_id']);
	}
	return $order;
}

if (JIMW_DEBUG_MODE) {
    		$jimw_config_db['profiler'] = true;
    		$debug = '';
}
// Connect to the global database :
$db_global = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);

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
	$list = list_tree($db, $prefix);
	$db->beginTransaction();
	reorder_tree(0, 1);
	$db->commit();
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