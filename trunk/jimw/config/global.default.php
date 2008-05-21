<?php
	/**
	 * JIMW Config
	 *
	 * @author	   Logisim
	 * @category   Jimw_Core
	 * @package    Jimw
	 * @copyright  Copyright (c) 2006-2008 jimw.fr
	 * @license    http://www.jimw.fr
	 * @version    $Id$
	 */
	if (!isset($jimw_config_db)) $jimw_config_db = array(); 
	/** @var jimw_config_db_name The name of the global database */
	if (!isset($jimw_config_db['dbname'])) $jimw_config_db['dbname'] = 'jimw_global';
	/** @var jimw_config_db_name The name of the global database */
	if (!isset($jimw_config_db['host'])) $jimw_config_db['host'] = 'localhost';
	/** @var jimw_config_db_type The type of the global database (sqlite, mysql, etc.)  */
	if (!isset($jimw_config_db['type'])) $jimw_config_db['type'] = 'PDO_MYSQL';

	/** @var jimw_config_db_username The username of the global database */
	if (!isset($jimw_config_db['username'])) $jimw_config_db['username'] = 'root';
	/** @var jimw_config_db_password The password of the global database */
	if (!isset($jimw_config_db['password'])) $jimw_config_db['password'] = '';

	/** Admin root directory */
	if (!defined('JIMW_ADMIN_ROOT')) define('JIMW_ADMIN_ROOT', JIMW_ROOT . 'admin/');
	
	/** JIMW lang */
	if (!defined('JIMW_LANG')) define('JIMW_LANG', 'fr');
	/** JIMW timezone */
	if (!defined('JIMW_TIMEZONE')) define('JIMW_TIMEZONE', 'Europe/Paris');

	/** @var jimw_debug_mode **/
	if (!defined('JIMW_DEBUG_MODE')) define('JIMW_DEBUG_MODE', false);
	
	/** Module directory */
	if (!defined('JIMW_REP_MODULE')) define('JIMW_REP_MODULE', JIMW_REP . 'module/');
	/** Public directory */
	if (!defined('JIMW_REP_PUBLIC')) define('JIMW_REP_PUBLIC', JIMW_ROOT . 'public/');
	/** Common public directory */
	if (!defined('JIMW_REP_PUBLIC_COMMON')) define('JIMW_REP_PUBLIC_COMMON', JIMW_REP_PUBLIC . 'common/');
	/** Public directory */
	if (!defined('JIMW_REP_INSTALL')) define('JIMW_REP_INSTALL', JIMW_ROOT . 'install/');
	/** Public directory */
	if (!defined('JIMW_REP_ADMIN_PUBLIC')) define('JIMW_REP_ADMIN_PUBLIC', JIMW_ADMIN_ROOT . 'public/');
	/** Lib directory */
	if (!defined('JIMW_REP_LIB')) define('JIMW_REP_LIB', JIMW_REP . 'lib/');
	/** Config directory */
	if (!defined('JIMW_REP_CONFIG')) define('JIMW_REP_CONFIG', JIMW_REP . 'config/');
	/** Lang directory */
	if (!defined('JIMW_REP_LANG')) define('JIMW_REP_LANG', JIMW_REP . 'lang/');
	/** Cache directory */
	if (!defined('JIMW_REP_CACHE')) define('JIMW_REP_CACHE', JIMW_ROOT . 'cache/');
	
	/** Module path in URL */
	if (!defined('JIMW_URL_MODULE_PATH')) define('JIMW_URL_MODULE_PATH', 'module');
	/** Default path in URL */
	if (!defined('JIMW_URL_DEFAULT_PATH')) define('JIMW_URL_DEFAULT_PATH', 'default');
	/** Admin path in URL */
	if (!defined('JIMW_URL_ADMIN_PATH')) define('JIMW_URL_ADMIN_PATH', '/admin');
	/** Public path in URL */
	if (!defined('JIMW_URL_PUBLIC_PATH')) define('JIMW_URL_PUBLIC_PATH', '/public');
	/** Public path in URL */
	if (!defined('JIMW_URL_PUBLIC_COMMON_PATH')) define('JIMW_URL_PUBLIC_COMMON_PATH', JIMW_URL_PUBLIC_PATH . '/common');
	/** Install path in URL */
	if (!defined('JIMW_URL_INSTALL_PATH')) define('JIMW_URL_INSTALL_PATH', '/install');

	/** Default extension (phtml or ajax) */
	if (!defined('JIMW_DEFAULT_EXT')) define('JIMW_DEFAULT_EXT', 'phtml');
	
	/** Jimw Database Prefix */
	if (!defined('JIMW_PREFIX')) define('JIMW_PREFIX', 'jimw');

	/** Use UTF8 for the website **/ 
	if (!defined('JIMW_UTF8')) define('JIMW_UTF8', false);
	
	/** URL rewriting */
	if (!defined('JIMW_URL_REWRITING')) define('JIMW_URL_REWRITING', true);
	
	/** Install Protect file path*/
	if (!defined('JIMW_INSTALL_PROTECTDIR')) define('JIMW_INSTALL_PROTECTDIR', 'protect/');
	
	/** Install Protect file prefix*/
	if (!defined('JIMW_INSTALL_PROTECTPREFIX')) define('JIMW_INSTALL_PROTECTPREFIX', 'install');
	
	/** Protect Install administration for a single domain */
	//define('JIMW_INSTALL_PROTECT_DOMAIN', 'www.jimw.fr');
	
	/** Thumbnails default directory or can be a prefix filename */
	if (!defined('JIMW_IMAGE_THUMBNAIL')) define('JIMW_IMAGE_THUMBNAIL_DIR', 'thumbnails/')
?>