<?php
/**
* Site internet du jeu Risk-Star Wars
*
* @version $Id: module_page.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
// gestion des modules page
$racine = PAGE_ROOT . REP_MODULE;
$fp = opendir($racine);
while ($page = readdir($fp))
{
  if (is_dir(PAGE_ROOT . REP_MODULE . $page) AND file_exists(PAGE_ROOT . REP_MODULE . $page . "/module_page.php")){
    include(PAGE_ROOT . REP_MODULE . $page . "/module_page.php");
  }
}
closedir($fp);
/**
if (file_exists(PAGE_ROOT . REP_MODULE . "$conf->get_module_page.php")){
	include(PAGE_ROOT . REP_MODULE . "$conf->get_module_page.php"); // fichier de $conf->geturation des modules
}
if (isset($module_page) AND is_array($module_page)) {
    while(list( ,$page) = each($module_page)){
		if (file_exists(PAGE_ROOT . REP_MODULE . $page . "/module_page.php"))
			include(PAGE_ROOT . REP_MODULE . $page . "/module_page.php");
	}
}
//*/

?>
