<?php
/**
* Site internet du jeu Risk-Star Wars
* 
* @version $Id: module.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
// gestion des modules
/*if (file_exists(PAGE_ROOT . REP_MODULE . ".php")){
	include(PAGE_ROOT . REP_MODULE . "$conf->get_module.php"); // fichier de $conf->geturation des modules   
	// include("."+REP_MODULE+"$conf->get_module.php");
}*/
if (!isset($module)){
	if (isset($_REQUEST['module']))
		$module = $_REQUEST['module'];
	else
		$module = "index";
}
if (file_exists(PAGE_ROOT . REP_MODULE . $module . "/module.php")){
	include(PAGE_ROOT . REP_MODULE . $module . "/module.php"); // fichier de $conf->geturation des modules   
	// include("."+REP_MODULE+"$conf->get_module.php");
}else{
	$temp->set_var("CONTENU","Le module $module n'existe pas, ou est incorect !");
	include(PAGE_ROOT . "include/fin.php");
}

?>