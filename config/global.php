<?php
	/**
	 * JIMW Config
	 *
	 * @author	    Patou
	 * @category   Jimw_Core
	 * @package    Jimw
	 * @copyright  Copyright (c) 2006-2007 jimw.fr
	 * @license    http://www.jimw.fr
	 * @version    $Id:  $
	 */
	 /** @var jimw_config_db_name The name of the global database */
	$jimw_config_db['dbname'] = 'jimw_global';
	/** @var jimw_config_db_name The name of the global database */
	$jimw_config_db['host'] = 'localhost';
	/** @var jimw_config_db_type The type of the global database (sqlite, mysql, etc.)  */
	$jimw_config_db['type'] = 'PDO_MYSQL';

	/** @var jimw_config_db_username The username of the global database */
	$jimw_config_db['username'] = 'root';
	/** @var jimw_config_db_password The password of the global database */
	$jimw_config_db['password'] = '';

	/** @var jimw_debug_mode **/
	define('JIMW_DEBUG_MODE', true);
	
	/** Module directory */
	define('JIMW_REP_MODULE', JIMW_REP . 'module/');
	
	/** Lib directory */
	define('JIMW_REP_LIB', JIMW_REP . 'lib/');
?>