<html><head><title>Administration de site du jeu Risk Star Wars</title>
<script type="text/javascript" src="./template/editor/fck$conf->get.js"></script>
<script type="text/javascript" src="./template/editor/fckeditor.js"></script>

		<script type="text/javascript">
window.onload = function()
{
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	var oFCKeditor = new FCKeditor( 'file' ) ;
	oFCKeditor.BasePath	= 'http://campmejtest.free.fr/template/editor/' ;
	oFCKeditor.ReplaceTextarea() ;
}

</script>
</head>
<body><H1>Administration de site du jeu Risk Star Wars</H1><br /><hr />
<?php

/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin2.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
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
	echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'>
	<input type='hidden' name='sub' value='1'>Titre : <input type='text' name='nom' value=''>
	<br />
	Texte :<br /><textarea id='file' name='file'>";
	//readfile(PAGE_ROOT . REP_ARTICLE . $nom . ".htm");
	echo "</textarea><br />
	<input type='submit' value='Ajouter'></form>";
}

if (isset($_GET['mod'])){

    echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'>
	<input type='hidden' name='id' value='", $_GET['id'], "'>
	<input type='hidden' name='sub' value='1'>Titre : <input type='text' name='nom' value='", $_GET['id'], "'>
	<br />
	Texte :<br /><textarea id='file' name='file'>";
	ob_start();
	readfile(PAGE_ROOT . REP_ARTICLE . $_GET['id']. ".htm");
	echo stripslashes(ob_get_clean());
	ob_end_clean();
	echo "</textarea><br />
	<input type='submit' value='Modifier'></form>";
}
echo "<a href ='" . ADMIN_NOM_PAGE . "add=1'>Ajouter</a><br/>";
//$news = mysql_query("SELECT id, titre, date, text FROM news ORDER BY id ASC", $sql);
$racine = PAGE_ROOT . REP_ARTICLE;
$fp = opendir($racine);
echo "<table border='1'><tr><th>Titre</th><th>Action</th></tr>";
while($page = readdir($fp)){
	if (ereg("^([a-zA-Z]*).htm$", $page)) {
		$nom = explode(".",$page);
		echo "<tr><td>", $nom[0], "</td><td><a href='", ADMIN_NOM_PAGE, "supp=1&id=", $nom[0], "'>Supprimer</a>&nbsp;&nbsp;<a href='", ADMIN_NOM_PAGE, "mod=1&id=", $nom[0], "'>Modifier</a></td></tr>";
	}
} // while
echo "</table>";
?>
