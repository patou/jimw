<script type="text/javascript" src="<?php echo PAGE_ROOT . REP_TEMPLATE . "FCKeditor"; ?>/fckeditor.js"></script>
<script type="text/javascript">
window.onload = function()
{
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	var oFCKeditor = new FCKeditor( 'file' ) ;
	oFCKeditor.BasePath	= '<?php echo PAGE_ROOT . REP_TEMPLATE . "FCKeditor/"; ?>' ;
	oFCKeditor.Width = 600 ;
	oFCKeditor.Height = 400 ;
	oFCKeditor.ReplaceTextarea() ;
}

</script>

<?php

/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin3.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
 * @copyright 2005
 **/
if (isset($_GET['supp'])) {
	//$sql_add = mysql_query("DELETE FROM `news` WHERE `id` = '" . $_GET['id'] . "'", $sql);
    @unlink(PAGE_ROOT . REP_ARTICLE . $_GET['id'] . '.htm');
	echo "Supression reussis !<br />";
}

if (isset($_POST['sub'])) {
	//$sql_add = mysql_query("INSERT INTO `news` ( `titre` , `date` , `text` ) VALUES ('" . $_POST['titre'] . "', '" . $_POST['date'] . "', '" . $_POST['text'] . "' );", $sql);
    $nom = $_POST['id'];
    if ($_POST['id'] <> $_POST['nom']) {
		$nom = $_POST['nom'];
		if (file_exists(PAGE_ROOT . REP_ARTICLE . $_POST['id'] . '.htm'))
		    @unlink(PAGE_ROOT . REP_ARTICLE . $_POST['id'] . '.htm');
    }
	$fp = fopen (PAGE_ROOT . REP_ARTICLE . $nom . ".htm", "w+");
    fwrite ($fp, stripslashes($_POST['file']));
    fclose ($fp);

	echo "Ajout/Modification reussis !<br />";
}

if (isset($_GET["add"])) {
	//echo "<script src='./template/whizzywig.js' type='text/javascript'></script>";
  echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'>
	<input type='hidden' name='sub' value='1'><table width='750' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Créer une page </H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='nom' value=''></td></tr>
	<tr><td bgcolor='#ffffcc' width='100'>Texte :</td><td><textarea id='file' name='file' style='width:600;height:350'>";
	//echo "<script type=\"text/javascript\">makeWhizzyWig(\"file\", \"all\");//1st param is ID of textarea, 2nd is list of buttons on toolbar</script>";
  echo "</textarea></td><td>
	<tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Créer'></td></tr></table></form>";
}

if (isset($_GET['mod'])){

    //echo "<script src='./template/whizzywig.js' type='text/javascript'></script>";
    echo "<form action='", ADMIN_NOM_PAGE, "' onsubmit=\"syncTextarea();\" method='POST'>
	<input type='hidden' name='id' value='", $_GET['id'], "'>
	<input type='hidden' name='sub' value='1'>
  <table width='750' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Modification de la page : ".$_GET['id']." </H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='nom' value='", $_GET['id'], "'></td></tr>
	<tr><td bgcolor='#ffffcc' width='100'>Texte :</td><td><textarea id='file' name='file' style='width:600;height:550'>";
	//ob_start();
	readfile(PAGE_ROOT . REP_ARTICLE . $_GET['id']. ".htm");
	//echo stripslashes(ob_get_clean());
	//ob_end_clean();
	echo "</textarea>";
  //echo "<script type=\"text/javascript\">makeWhizzyWig(\"file\", \"all\");//1st param is ID of textarea, 2nd is list of buttons on toolbar</script>";
echo "</td><td>
	<tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Modifier'></td></tr></table></form>";
}
echo "<a href ='" . ADMIN_NOM_PAGE . "add=1'>Ajouter</a><br/>";
//$news = mysql_query("SELECT id, titre, date, text FROM news ORDER BY id ASC", $sql);
echo '
<script language="javascript">
<!--
function supprimer(id){
  if (confirm("Etes vous sur de supprimer cette éléments ?")){
    window.location = "' . ADMIN_NOM_PAGE . 'supp=1&id=" + id;
  }
}
-->
</script>';
$racine = PAGE_ROOT . REP_ARTICLE;
$fp = opendir($racine);
echo "<table border='1' bordercolor='#339933' cellspacing='0' width='600'><tr bgcolor='#339933'><th>Titre de la Page</th><th>Action</th></tr>";
while($page = readdir($fp)){
	if (ereg("^([a-zA-Z0-9_]*).htm$", $page)) {
		$nom = explode(".",$page);
		echo "<tr bgcolor='#ffffcc'><td>", $nom[0], "</td><td>
        <a href='javascript:supprimer(", $nom[0], ");'><img src='./images/supprimer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;<a href='", ADMIN_NOM_PAGE, "mod=1&id=", $nom[0], "'><img src='./images/editer.gif'  border='0' width='17' height='17' ></a></td></tr>";
	}
} // while
echo "</table>";
?>
