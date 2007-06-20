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
error_reporting(E_ALL|E_STRICT);
if (file_exists('config/global.php'))
	include('config/global.php');
if (empty($JIMW_REP))
	$JIMW_REP = 'jimw/';
set_include_path($JIMW_REP . '/lib' . PATH_SEPARATOR . $JIMW_REP . PATH_SEPARATOR . get_include_path()); 
require_once('Zend/Loader.php');
spl_autoload_register(array('Zend_Loader', 'autoload'));
try {
	new Jimw_Controler ();
}
catch (Zend_Exception $e) {
	echo $e->getMessage();
}
catch (Exception $e) {
	echo $e->getMessage();
}
?>