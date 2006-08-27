<?php
/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin.php,v 1.1 2006/05/22 12:02:59 lesgrumels Exp $
 * @copyright 2005
 **/

if (isset($_GET['supp'])) {
	$sql_add = $sql->doQuery("DELETE FROM news WHERE id = '" . $_GET['id'] . "'");
    echo "Supression reussis !<br />";
}
if (isset($_POST['sadd'])) {
  $req = $sql->doQuery("SELECT Max(id) + 1 AS maxid FROM news"); //nouveau ID
	$tmp = $sql->fetch($req);
	$id = (isset($tmp->maxid) AND !empty($tmp->maxid))?$tmp->maxid:1;
	$sql_add = $sql->doQuery('INSERT INTO news (id, titre , date , text )
VALUES ("' . $id . '", "' . $_POST['titre'] . '", "' . $_POST['date'] . '", "' . protege_texte($_POST['text']) . '");');
    echo "Ajout reussis !<br />";
}
if (isset($_POST['smod'])) {
	$sql_mod = $sql->doQuery("UPDATE news SET titre = '" . $_POST['titre'] . "', date = '" . $_POST['date'] . "', text = '" . protege_texte($_POST['text']) . "' WHERE id = '" . $_POST['id'] . "'");
    echo "Modification réussis !<br />";
}
if (isset($_GET["add"])) {
  echo '<script src="' .PAGE_ROOT . REP_EDITOR .'/whizzywig/francais.js" type="text/javascript"></script>
  <script src="' .PAGE_ROOT . REP_EDITOR .'/whizzywig/whizzywig.js" type="text/javascript"></script>';
	echo "<form action='", ADMIN_NOM_PAGE, "' onsubmit=\"syncTextarea();\" method='POST'>
    <input type='hidden' name='id' value='", $_GET['id'], "'><input type='hidden' name='sadd' value='1'>
        <table width='650' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Ajouter une news </H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='titre' value='' size='20'></td></tr>
    <tr><td bgcolor='#ffffcc'>Date : </td><td><input type='text' name='date' value='", date("d/m/Y"), "' size='10'>(jjmmaaaa)</td></tr>
    <tr><td bgcolor='#ffffcc'>Texte :</td><td><textarea name='text' id ='text' style='width:420;height:200'></textarea>
    <script type=\"text/javascript\">makeWhizzyWig(\"text\", \"formatblock fontname fontsize newline  bold italic underline | number bullet | undo redo  | color hilite | link html spellcheck\");//1st param is ID of textarea, 2nd is list of buttons on toolbar</script></td></tr>
    <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Ajouter'></td></tr></table></form>";

}
if (isset($_GET['mod'])){
        $sql_mod = $sql->doQuery("SELECT titre, date, text FROM news WHERE id LIKE '" . $_GET['id'] . "'");
	$row = $sql->fetch_array($sql_mod);
	  echo '<script src="' .PAGE_ROOT . REP_EDITOR .'/whizzywig/francais.js" type="text/javascript"></script>
  <script src="' .PAGE_ROOT . REP_EDITOR .'/whizzywig/whizzywig.js" type="text/javascript"></script>';
    echo "<form action='", ADMIN_NOM_PAGE, "' onsubmit=\"syncTextarea();\" method='POST'>
    <input type='hidden' name='id' value='", $_GET['id'], "'><input type='hidden' name='smod' value='1'>
        <table width='650' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Modification des news </H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='titre' value='", $row[0], "' size='20'></td></tr>
    <tr><td bgcolor='#ffffcc'>Date : </td><td><input type='text' name='date' value='",$row[1], "' size='10'>(jj/mm/aaaa)</td></tr>
    <tr><td bgcolor='#ffffcc'>Texte :</td><td><textarea name='text' id='text' style='width:400;height:200'>", deprotege_texte($row[2]), "</textarea>
    <script type=\"text/javascript\">makeWhizzyWig(\"text\", \"formatblock fontname fontsize newline  bold italic underline | number bullet | undo redo  | color hilite | link html spellcheck\");//1st param is ID of textarea, 2nd is list of buttons on toolbar</script></td></tr>
    <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Modifier'></td></tr></table></form>";

}
echo "<a href ='" . ADMIN_NOM_PAGE . "add=1'>Ajouter</a><br/>";
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
$news = $sql->doQuery("SELECT id, titre, date, text FROM news ORDER BY id ASC");
echo "<table border='1' bordercolor='#339933' cellspacing='0' width='600'><tr bgcolor='#339933'><th>Id</th><th>Titre</th><th>Date</th><th>Action</th></tr>";
while($row = $sql->fetch_array($news)){
	echo "<tr bgcolor='#ffffcc'><td>", $row[0], "</td><td>", $row[1], "</td><td>", $row[2], "</td>
      <td><a href='javascript:supprimer(", $row[0], ");'><img src='./images/supprimer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;
      <a href='", ADMIN_NOM_PAGE, "mod=1&id=", $row[0], "'><img src='./images/editer.gif'  border='0' width='17' height='17' ></a></td></tr>";
} // while
echo "</table>";
?>
