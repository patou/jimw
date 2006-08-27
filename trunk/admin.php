<?php 
define("PAGE_ROOT","./");
include(PAGE_ROOT . 'extention.inc');
include_once(PAGE_ROOT . "config/config.php");
include(PAGE_ROOT . "include/constantes.php");
include(PAGE_ROOT . "include/fonction.php");
if (file_exists(PAGE_ROOT . "include/db/db_" . SQL_TYPE . ".php"))
  include_once(PAGE_ROOT . "include/db/db_" . SQL_TYPE . ".php");
else
  exit('La Base de Donnée n\'existe pas');
include(PAGE_ROOT . "include/config.php");
$sql = new DBTools(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);
$sql->connect();
echo $sql->Error();
$conf = new config('admin');
include(PAGE_ROOT . "include/auth.php");
if (empty($_GET["action"])):
?>
<head>
<title>Administration de <?php echo $conf->get('nom_site') ?></title>
<meta http-equiv="Content-Type" content="text/html;">
</head>
<frameset cols="170,*" rows="*" border="2" framespacing="0" frameborder="yes">
  <frame src="<?php echo $_SERVER['SCRIPT_NAME'];?>?action=menu" marginwidth="3" marginheight="3" scrolling="auto">
  <frame src="<?php echo $_SERVER['SCRIPT_NAME'];?>?action=page" name="page" marginwidth="10" marginheight="10" scrolling="auto">
</frameset>

<noframes>
	<body bgcolor="#FFFFFF" text="#000000">
		<p>D&eacute;soler votre navigateur ne reconnait pas les frames</p>
	</body>
</noframes>
</html>
<?php 
elseif ($_GET["action"]=="menu"):
?>
<html><head><title>Menu de l'administration de <?php echo $conf->get('nom_site') ?></title></head><body>
<img src="<?php echo $conf->get('logo') ?>" width="150" ><br>
<a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?action=page" target="page">Accueil</a><br><hr>
<?php 
$racine = PAGE_ROOT . REP_MODULE;
$fp = opendir($racine);
$i = 0;
$listmodule = array();
while ($module = readdir($fp))
{
  if (is_dir($racine . $module) AND file_exists($racine . $module . "/module_admin.php")){
   if (file_exists($racine . $module . "/menu_admin.php")){
     include($racine . $module . "/menu_admin.php");
     echo '<a href="', $_SERVER['SCRIPT_NAME'], '?action=module&module='.$module.'" target="page" title="" onclick="fermer_menu();document.getElementById(\''.$module.'\').style.display=\'block\';">'.$menu_name.'</a><br>';
     /*echo "<a href='admin.php?action=module&module=".$module."' target='page' title='' onclick=\"if (document.getElementById('".$module."').style.display=='none') {
				document.getElementById('".$module."').style.display='block';}else { document.getElementById('".$module."').style.display='none';}\">$menu_name</a><br>";
     */
     echo "<div id='".$module."' style='display:none'>";
     if (isset($sous_menu)){
     while (list(,list($var, $nom)) = each($sous_menu))
       echo '&nbsp;>&nbsp;&nbsp;<a href="', $_SERVER['SCRIPT_NAME'], '?action=module&module='.$module.'&'.$var.'" target="page" title="">'.$nom.'</a><br>';
    }
     echo "</div>";
     $listmodule[] = $module;
   }
   else
     echo '<a href="', $_SERVER['SCRIPT_NAME'], '?action=module&module='.$module.'" target="page" title="">$module</a><br>';
 }
}
echo '
<script language="javascript">
<!--
function fermer_menu(){';
while (list(,$module) = each($listmodule))
  echo "document.getElementById('".$module."').style.display='none';";
echo '}
-->
</script>';
closedir($fp);
?>
<br><hr>
<a href="http://www.lesgrumels.fr" target="page">Aide</a><br>
<a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?logout=1" target="_parent" >Déconnexion</a><br>
</body></html>
<?php 
elseif ($_GET["action"]=="page"):
?>
<html><head><title>Administration de <?php echo $conf->get('nom_site') ?></title></head>
<body><H1>Administration de <?php echo $conf->get('nom_site') ?></H1><br>
<form action="<?php echo $_SERVER['SCRIPT_NAME'];?>?action=page" method="POST">
<input type="hidden" name="action" value="page"/>
<textarea name="message" cols="100" rows="20">
<?php
if (isset($_POST['message']))
{
   $fp = fopen(PAGE_ROOT . 'config/message.txt', 'w');
   fwrite($fp, $_POST['message']);
   fclose($fp); 
}
echo  stripslashes(file_get_contents(PAGE_ROOT . 'config/message.txt'));
?>
</textarea><br />
<input type="submit" value="Sauvegarder">
</form>
<br /><br></body></html>
<?php 
elseif ($_GET["action"]=="module"):
	if (file_exists(PAGE_ROOT . REP_MODULE . $_GET["module"] . "/module_admin.php")) {
		define("ADMIN_NOM_PAGE", "admin.php?action=module&module=" . $_GET["module"] . "&");
		if (!isset($_GET['echo_off']))
		{
		?>
		<html><head><title>Administration de <?php echo $conf->get('nom_site') ?></title></head>
<body><H1>Administration de <?php echo $conf->get('nom_site') ?></H1><br>
<?php 
		}
		include(PAGE_ROOT . REP_MODULE . $_GET["module"] . "/module_admin.php");
?>
<br /><br></body></html>
<?php 
	}
	else
		header("location:admin.php?action=page");
endif;
?>