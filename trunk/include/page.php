<?php
/**
* Site internet du jeu Risk-Star Wars
*
* @version $Id: page.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
include(PAGE_ROOT . 'extention.inc');
include_once(PAGE_ROOT . "include/constantes.php");
include_once(PAGE_ROOT . "config/config.php");
include_once(PAGE_ROOT . "include/fonction.php");
include_once(PAGE_ROOT . "include/template.php");
if (file_exists(PAGE_ROOT . "include/db/db_" . SQL_TYPE . ".php"))
  include_once(PAGE_ROOT . "include/db/db_" . SQL_TYPE . ".php");
else
  exit('La Base de Donnée n\'existe pas');
include_once(PAGE_ROOT . "include/config.php");
// Connection a la base de donnée
$sql = new DBTools(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);
$sql->connect();
echo $sql->Error();
$conf = new config('site');
// debut de le génération de la page
$temp = new Template (PAGE_ROOT . REP_TEMPLATE . $conf->get("template") . '/', "remove");
$temp->set_file("page" , "page.htm");//definie une page pour le site !
$temp->set_var(array("HOST_TEMP" => PAGE_ROOT . REP_TEMPLATE . $conf->get("template") . '/',
					           "HOST" => PAGE_ROOT,
					           "NOM_PAGE" => $_SERVER['SCRIPT_NAME'],
                     "COLOR1" => $conf->get("COLOR1"),
                     "COLOR2" => $conf->get("COLOR2"),
                     "COLOR3" => $conf->get("COLOR3"),
                     "COLORTEXT1" => $conf->get("COLORTEXT1"),
                     "COLORTEXT2" => $conf->get("COLORTEXT2"),
                     "COLORTEXT3" => $conf->get("COLORTEXT3"),
                     "FONT_FACE" => $conf->get("FONT_FACE"),
                     "DESCRIPTION" => $conf->get("DESCRIPTION"),
                     "MOTSCLE" => $conf->get("MOTSCLE"),
                     "CREDIT" =>" &copy;2005 MEJ"));//ajoute les variables
include(PAGE_ROOT . "include/module_page.php");//cr?ion du menu !
/*if (!defined("INSTALLER"))
	header('location:'.PAGE_ROOT . "install.php");*/
if (isset($CONNECT_USER)){ // connexion des utilisateurs requise
	include(PAGE_ROOT . "include/auth.php");
}
// affichage de la page
include(PAGE_ROOT . "include/module.php");
// fin de la page
include(PAGE_ROOT . "include/fin.php");

?>
