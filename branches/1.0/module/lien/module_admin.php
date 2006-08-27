<?php

/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin.php,v 1.1 2006/05/22 12:03:07 lesgrumels Exp $
 * @copyright 2005
 **/
function set_cat($cat = ''){
 global $sql;
 $result = $sql->doQuery('SELECT id, titre FROM cat_lien ORDER BY place');
 echo "<select name='catego'>";
 while($row = $sql->fetch_array($result)){
 	echo "<option value='", $row[0], "' ", ($row[0]==$cat)?"SELECTED>":">", $row[1], "</option>";
 } // while
 echo "</select><br/>";
}

if (isset($_REQUEST['cat'])){
  if (isset($_GET['supp'])) {
    $req = $sql->doQuery('SELECT place FROM cat_lien WHERE id = "' . $_GET['id'] . '"');
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
	 	if ($sql_up = $sql->doQuery('UPDATE cat_lien SET place = place - 1 WHERE place > ' . $place))
  	 if ($sql_sup = $sql->doQuery('DELETE FROM cat_lien WHERE id LIKE "' . $_GET['id'] . '"'))
        echo 'Supression reussis !<br />';
      else
        echo '<font color=red>Echec de la suppresion</font>';
    else
      echo '<font color=red>Echec de la suppression (réorganisation des places)</font>';
  }
  if (isset($_POST['sadd'])) {
    $req = $sql->doQuery("SELECT Max(id) + 1 AS maxid, Max(place) + 1 AS place FROM cat_lien"); //nouveau ID
	  $tmp = $sql->fetch($req);
	  $id = (isset($tmp->maxid) AND !empty($tmp->maxid))?$tmp->maxid:1;
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
	 	if ($sql_add = $sql->doQuery('INSERT INTO cat_lien ( id , place, titre , description )
  VALUES ("'.$id.'", "'.$place.'", "' . $_POST['titre'] . '", "' . $_POST['description'] . '");'))
      echo 'Ajout reussis !<br />';
    else
      echo '<font color=red>Echec de l\'ajout</font>';
  }
  if (isset($_POST['smod'])) {
  	$sql_mod = $sql->doQuery("UPDATE cat_lien SET titre = '" . $_POST['titre'] . "', description = '" . $_POST['description'] . "' WHERE id = '" . $_POST['id'] . "'");
      echo "Modification réussis !<br />";
  }
  if (isset($_GET["add"])) {
  	echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'><input type='hidden' name='sadd' value='1'><input type='hidden' name='cat' value='1'>
    <table width='500' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
        <tr>
        <td colspan='2' bgcolor='#339933'>
          <div align='center'><H3>Ajouter une catégorie</H3> </div>
        </td>
      </tr>
      <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='titre' value=''></td></tr>
      <tr valign='top'><td bgcolor='#ffffcc' width='100'>Description :</td><td valign='top'><textarea style='width:300;height:50' name='description'></textarea></td></tr>
      <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Ajouter'></td></tr></table></form>";

  }
  if (isset($_GET['mod'])){
      $sql_mod = $sql->doQuery("SELECT titre, description FROM cat_lien WHERE id LIKE '" . $_GET['id'] . "'");
  	$row = $sql->fetch_array($sql_mod);
      echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'><input type='hidden' name='id' value='", $_GET['id'], "'>
      <input type='hidden' name='smod' value='1'><input type='hidden' name='cat' value='1'>
      <table width='500' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
        <tr>
        <td colspan='2' bgcolor='#339933'>
          <div align='center'><H3>Modification des Catégorie</H3> </div>
        </td>
      </tr>
      <tr><td bgcolor='#ffffcc' width='100'>Titred : </td><td><input type='text' name='titre' value='", $row[0], "'></td></tr>
      <tr valign='top'><td bgcolor='#ffffcc' width='100'>Description :</td><td><textarea style='width:300;height:50' name='description'>", $row[1], "</textarea></td></tr>
      <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Modifier'></td></tr></table></form>";
  }
  if (isset($_GET['up'])){
    $req = $sql->doQuery('SELECT place FROM cat_lien WHERE id = "' . $_GET['id'] . '"');
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
    $sql_up = $sql->doQuery('UPDATE cat_lien SET place = ' . $place . ' WHERE place = ' . ($place  - 1));
    $sql_up = $sql->doQuery('UPDATE cat_lien SET place = ' . ($place - 1) . ' WHERE id =  ' . $_GET['id']);
    echo $sql->error();
  }
  if (isset($_GET['down'])){
    $req = $sql->doQuery('SELECT place FROM cat_lien WHERE id = "' . $_GET['id'] . '"');
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
    $sql_up = $sql->doQuery('UPDATE cat_lien SET place = ' . $place . ' WHERE place = ' . ($place + 1));
    $sql_up = $sql->doQuery('UPDATE cat_lien SET place = ' . ($place + 1) . ' WHERE id =  ' . $_GET['id']);
    echo $sql->error();

  }
  echo "<a href ='" . ADMIN_NOM_PAGE . "cat=1&add=1'>Ajouter</a>&nbsp;&nbsp;<a href ='" . ADMIN_NOM_PAGE . "'>Les liens</a><hr/>";
  $news = $sql->doQuery("SELECT DISTINCT id , place, titre, description
  FROM cat_lien
  ORDER BY place ASC");
echo '
<script language="javascript">
<!--
function supprimer(id){
  if (confirm("Etes vous sur de supprimer cette éléments ?")){
    window.location = "' . ADMIN_NOM_PAGE . 'cat=1&supp=1&id=" + id;
  }
}
-->
</script>';
  echo "<table border='1' bordercolor='#339933' cellspacing='0' width='600'><tr bgcolor='#339933'><th>Place</th><th>Titre</th><th>Description</th><th>Action</th></tr>";
  $first = True;
  $lastid = 0;
  while($row = $sql->fetch_array($news)){
    if (!$first)
      echo "<a href='", ADMIN_NOM_PAGE, "cat=1&down=1&id=", $lastid, "'><img src='./images/down.gif'  border='0' width='17' height='17' ></a></td></tr>";
  	echo "<tr bgcolor='#ffffcc'><td>", $row[1], "</td><td>", $row[2], "</td><td>", $row[3], "</td>
        <td><a href='javascript:supprimer(", $row[0], ")'><img src='./images/supprimer.gif'  border='0' width='17' height='17' ></a>
        &nbsp;&nbsp;<a href='", ADMIN_NOM_PAGE, "cat=1&mod=1&id=", $row[0], "'><img src='./images/editer.gif'  border='0' width='17' height='17' ></a>",
        (!$first?"<a href='". ADMIN_NOM_PAGE . "cat=&&up=1&id=" . $row[0] . "'><img src='./images/up.gif'  border='0' width='17' height='17' ></a>":"<img src='./images/spacer.gif'  border='0' width='17' height='17' >");
    $first = False;
	  $lastid = $row[0];
  } // while
  echo "</td></tr></table>";

} //if isset $cat
else{
  if (isset($_GET['supp'])) {
  	if ($sql_sup = $sql->doQuery('DELETE FROM lien WHERE id LIKE "' . $_GET['id'] . '"'))
      echo "Supression reussis !<br />";
  }
  if (isset($_POST['sadd'])) {
    $req = $sql->doQuery('SELECT Max(id) + 1 AS maxid FROM lien'); //nouveau ID
	  $tmp = $sql->fetch($req);
	  $id = (isset($tmp->maxid) AND !empty($tmp->maxid))?$tmp->maxid:1;
  	if ($sql_add = $sql->doQuery('INSERT INTO lien ( id , cat , titre , description , url )
          VALUES ("' . $id . '", "' . $_POST['catego'] . '", "' . $_POST['titre'] . '", "' . $_POST['description'] . '", "' . $_POST['lien'] . '");'))
      echo "Ajout reussis !<br />";
  }
  if (isset($_POST['smod'])) {
  	if ($sql_mod = $sql->doQuery('UPDATE lien SET cat = "' . $_POST['catego'] . '", titre = "' . $_POST['titre'] . '",
        url = "' . $_POST['lien'] . '", description = "' . $_POST['description'] . '" WHERE id = "' . $_POST['id'] . '"'))
      echo "Modification réussis !<br />";
  }
  if (isset($_GET["add"])) {
  	echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'><input type='hidden' name='sadd' value='1'><table width='500' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
        <tr>
        <td colspan='2' bgcolor='#339933'>
          <div align='center'><H3>Ajouter un lien</H3> </div>
        </td>
      </tr>
      <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='titre' value=''></td></tr>
      <tr><td bgcolor='#ffffcc' width='100'>Catégorie : </td><td>", set_cat(), "</td></tr>
      <tr><td bgcolor='#ffffcc' width='100'>Lien : </td><td><input type='text' size='50' name='lien' value=''></td></tr>
      <tr valign='top'><td bgcolor='#ffffcc' width='100'>Texte :</td><td valign='top'><textarea style='width:300;height:50' name='description'></textarea></td></tr>
      <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Ajouter'></td></tr></table></form>";

  }
  if (isset($_GET['mod'])){
      $sql_mod = $sql->doQuery('SELECT cat, titre, url, description FROM lien WHERE id LIKE "' . $_GET['id'] . '"');
  	$row = $sql->fetch_array($sql_mod);
      echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'><input type='hidden' name='id' value='", $_GET['id'], "'><input type='hidden' name='smod' value='1'>
      <table width='500' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
        <tr>
        <td colspan='2' bgcolor='#339933'>
          <div align='center'><H3>Modification des liens</H3> </div>
        </td>
      </tr>
      <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' name='titre' value='", $row[1], "'></td></tr>
      <tr><td bgcolor='#ffffcc' width='100'>Catégorie : </td><td>", set_cat($row[0]), "</td></tr>
      <tr><td bgcolor='#ffffcc' width='100'>Lien : </td><td><input type='text' name='lien' size='50' value='", $row[2], "'></td></tr>
      <tr valign='top'><td bgcolor='#ffffcc' width='100'>Texte :</td><td><textarea style='width:300;height:50' name='description'>", $row[3], "</textarea></td></tr>
      <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Modifier'></td></tr></table></form>";
  }
  echo "<a href ='" . ADMIN_NOM_PAGE . "add=1'>Ajouter</a>&nbsp;&nbsp;<a href ='" . ADMIN_NOM_PAGE . "cat=1'>Les Catégorie</a><hr />";
  $news = $sql->doQuery('SELECT DISTINCT lien . id , cat_lien . titre AS cat , lien . url AS url , lien . titre AS titre, lien . description AS description
  FROM cat_lien , lien
  WHERE lien . cat = cat_lien . id
  ORDER BY lien . titre ASC');
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
  echo "<table border='1' bordercolor='#339933' cellspacing='0' width='600'><tr bgcolor='#339933'><th>ID</th><th>Cat</th><th>Lien</th><th>Titre</th><th>Action</th></tr>";
  while($row = $sql->fetch_array($news)){
  	echo "<tr bgcolor='#ffffcc'><td>", $row[0], "</td><td>", $row[1], "</td><td>", $row[2], "</td><td>", $row[3], "</td>
    <td><a href='javascript:supprimer(", $row[0], ")'><img src='./images/supprimer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;<a href='", ADMIN_NOM_PAGE, "mod=1&id=", $row[0], "'><img src='./images/editer.gif'  border='0' width='17' height='17' ></a></td></tr>";
  } // while
  echo "</table>";
}
?>
