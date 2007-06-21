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
if (!empty($jimw_debug_mode))
	error_reporting(E_ALL|E_STRICT);
if (file_exists('config/global.php'))
	include('config/global.php');
if (empty($JIMW_REP))
	$JIMW_REP = './jimw/';
set_include_path($JIMW_REP . 'lib' . PATH_SEPARATOR . 'lib' . PATH_SEPARATOR . $JIMW_REP . PATH_SEPARATOR . get_include_path()); 
require_once('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader', 'autoload'));
try {
}
catch (Zend_Exception $e) {
	if (!empty($jimw_debug_mode))
		echo $e->getMessage();
	else
		echo 'error'; /** @todo Change it to more friendly error*/
}
catch (Exception $e) {
	if (!empty($jimw_debug_mode))
		echo $e->getMessage();
	else
		echo 'error'; /** @todo Change it to more friendly error*/
}

?>