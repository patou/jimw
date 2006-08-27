<?php

/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin.php,v 1.1 2006/05/22 12:03:14 lesgrumels Exp $
 * @copyright 2005
 **/
/* function privée de aff_cat */
function aff_cat_rec($tableau, $idparent, $parent = 0, $prof = 1)
{
  if (isset($tableau[$parent]))
  {
    for($deca='',$i=0;$i<$prof;$i++)
      $deca .= '&nbsp;>&nbsp;';
    while(list($id, $titre) = each($tableau[$parent]))
    {
    	  echo '<option value="', $id, '" ', (($id == $idparent)?'SELECTED':''), '>', $deca, deprotege_texte($titre), '</option>' ;
      	aff_cat_rec($tableau, $idparent, $id, $prof+1);
    }

  }
}
/* affiche un menu déroulant avec tous le menu hiérarchique */
function aff_cat($parent){
  global $sql;
    $sql_par = $sql->doQuery('SELECT id, parent, titre FROM menu ORDER BY place');
    echo '<select name="parent"><option value="0" ', (($parent == 0)?'SELECTED':''), '>racine</option>';
    $h = array();
		$i = 0;
    while ($Enr=$sql->fetch($sql_par))
		{
			   $h[$Enr->parent][$Enr->id] = $Enr->titre;
			   $i++;
		}
	  aff_cat_rec($h, $parent);
    echo '</select>';
}
/* affiche la liste de lien du menu */
function aff_lien($lien = '')
{
  global $sql;
  $racine = PAGE_ROOT . REP_MODULE;
  $fp = opendir($racine);
  $i = 0;
  echo '<select name="url_list" onChange="document.getElementById(\'url\').value = this.value">
  <option ', (($lien == '')?'SELECTED':''), '>Selectionner un lien</option>';
  while ($module = readdir($fp))
  {
    if (is_dir($racine . $module) AND file_exists($racine . $module . "/menu.php")){
       include($racine . $module . "/menu.php");
       echo '<optgroup label="'.$menu_name.'">';
       if (isset($sous_menu)){
          while (list(,list($var, $nom)) = each($sous_menu))
            echo '<option value="'.$var.'"  ', (($lien == $var)?'SELECTED':''), '>'.$nom.'</option>';
       }
       echo "</optgroup>";
    }
  }
  echo "</select>";
  closedir($fp);
}
//valeur du target
$tab_target = array('' => '' , '_self' => 'Même fenêtre', '_blank' => 'Nouvelle page');

$parent = (isset($_REQUEST['parent']) AND !empty($_REQUEST['parent']))?$_REQUEST['parent']:0;
define("ADMIN_NOM_PAGE_MENU", ADMIN_NOM_PAGE . 'parent=' . $parent . '&');

if (isset($_GET['supp'])) {
    $req = $sql->doQuery('SELECT place FROM menu WHERE id = "' . $_GET['id'] . '"');
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
	 	if ($sql_up = $sql->doQuery('UPDATE menu SET place = place - 1 WHERE place > ' . $place . ' AND parent LIKE ' . $parent))
  	 if ($sql_sup = $sql->doQuery('DELETE FROM menu WHERE id LIKE "' . $_GET['id'] . '"'))
        echo 'Supression reussis !<br />';
      else
        echo '<font color=red>Echec de la suppresion</font>';
    else
      echo '<font color=red>Echec de la suppression (réorganisation des places)</font>';
  }
if (isset($_REQUEST['add'])) {
  if ($_REQUEST['add']==2) {
    $req = $sql->doQuery('SELECT Max(id) + 1 AS maxid FROM menu'); //nouveau ID
	  $tmp = $sql->fetch($req);
	  $id = (isset($tmp->maxid) AND !empty($tmp->maxid))?$tmp->maxid:1;
	  $req = $sql->doQuery('SELECT Max(place) + 1 AS place FROM menu WHERE parent LIKE ' . $parent); //nouveau ID
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
	 	if ($sql_add = $sql->doQuery('INSERT INTO menu ( id, parent, place, target, titre, url , description)
  VALUES ("'.$id.'", "'.$parent.'", "'.$place.'", "' . $_POST['target'] . '", "' . protege_texte($_POST['titre']) . '", "' . protege_texte($_POST['url']) . '", "' . protege_texte($_POST['description']) . '");'))
      echo 'Ajout reussis !<br />';
    else
      echo '<font color=red>Echec de l\'ajout</font>';
  }
  else
    {
	  echo "<form action='", ADMIN_NOM_PAGE_MENU, "' method='POST'>
    <input type='hidden' name='add' value='2'>
        <table width='650' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Ajouter au menu </H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' id='titre' name='titre' value='' size='35'></td></tr>
    <tr><td bgcolor='#ffffcc'>Lien : </td><td>
    ";
    aff_lien();
    echo "<br /><input type='text' name='url' id='url' value='' size='50'><input type='button' value='Essai' onclick='window.open(document.getElementById(\"url\").value,document.getElementById(\"titre\").value,\"dialogHeight:600px;dialogWidth:800px;help:no;scroll:yes;status:no\")'></td></tr>
    <tr><td bgcolor='#ffffcc'>Description :</td><td><textarea name='description' style='width:300;height:50'></textarea></td></tr>
    <tr><td bgcolor='#ffffcc' width='100'>Destination : </td><td><select name='target'>";
    while(list($targ, $target) = each($tab_target))
      echo '<option value="', $targ,'" ' , ($targ == '')?'SELECTED':'', '>', $target, '</option>';
    echo "</select></td></tr>
    <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Ajouter'></td></tr></table></form>";
    }
}
if (isset($_REQUEST['mod'])){
   if ($_REQUEST['mod']==2) {
	  if ($sql_mod = $sql->doQuery("UPDATE menu SET titre = '" . protege_texte($_POST['titre']) . "', url = '" . protege_texte($_POST['url']) . "', description = '" .
                                  protege_texte($_POST['description']) . "', target = '" . $_POST['target'] ."' WHERE id = '" . $_POST['id'] . "'"))
    echo "Modification réussis !<br />";
}
else{
    $sql_mod = $sql->doQuery('SELECT * FROM menu WHERE id LIKE ' . $_REQUEST['id'] . ' AND parent LIKE ' . $parent);
	  $row = $sql->fetch($sql_mod);
    echo "<form action='", ADMIN_NOM_PAGE_MENU, "' method='POST'>
    <input type='hidden' name='id' value='", $_REQUEST['id'], "'><input type='hidden' name='mod' value='2'>
        <table width='650' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Modification du menu </H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='100'>Titre : </td><td><input type='text' id='titre' name='titre' value='", deprotege_texte($row->titre), "' size='35'></td></tr>
    <tr><td bgcolor='#ffffcc'>Lien : </td><td>
    ";
    aff_lien(deprotege_texte($row->url));
    echo "<br /><input type='text' name='url' id='url' value='", deprotege_texte($row->url),"' size='50'><input type='button' value='Essai' onclick='window.open(document.getElementById(\"url\").value,document.getElementById(\"titre\").value,\"dialogHeight:600px;dialogWidth:800px;help:no;scroll:yes;status:no\")'></td></tr>
    <tr><td bgcolor='#ffffcc'>Description :</td><td><textarea name='description' style='width:300;height:50'>", deprotege_texte($row->description), "</textarea></td></tr>
    <tr><td bgcolor='#ffffcc' width='100'>Destination : </td><td><select name='target'>";
    while(list($targ, $target) = each($tab_target))
      echo '<option value="', $targ,'" ' , ($targ == $row->target)?'SELECTED':'', '>', $target, '</option>';
    echo "</select></td></tr>
    <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Modifier'></td></tr></table></form>";
}}
if (isset($_REQUEST['mov'])){
   if ($_REQUEST['mov']==2 AND $parent != $_POST['vparent']) {
    $req = $sql->doQuery('SELECT Max(place) + 1 AS place FROM menu WHERE parent LIKE ' . $parent);
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
	  $req = $sql->doQuery('SELECT Max(place) + 1 AS place FROM menu WHERE parent LIKE ' . $_POST['vparent']);
	  $tmp = $sql->fetch($req);
	  $vplace = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
	  if ($sql_up = $sql->doQuery('UPDATE menu SET place = place - 1 WHERE place > ' . $vplace . ' AND parent LIKE ' . $_POST['vparent']))
	     if ($sql_mod = $sql->doQuery('UPDATE menu SET parent = "' . $_POST['parent'] . '", place = "' . $place . '" WHERE id = "' . $_POST['id'] . '"'))
          echo "Déplacement réussis !<br />";
  }
  else{
    $sql_mod = $sql->doQuery('SELECT parent, titre FROM menu WHERE id LIKE ' . $_REQUEST['id'] . ' AND parent LIKE ' . $parent);
	  $row = $sql->fetch($sql_mod);
    echo "<form action='", ADMIN_NOM_PAGE, "' method='POST'>
    <input type='hidden' name='id' value='", $_REQUEST['id'], "'><input type='hidden' name='mov' value='2'>
    <input type='hidden' name='vparent' value='", $parent, "'>
        <table width='650' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0'>
      <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H3>Déplacement du menu  ", deprotege_texte($row->titre), "</H3> </div>
      </td>
    </tr>
    <tr><td bgcolor='#ffffcc' width='150'>Nouveau menu Parent : </td><td>
    ";
    aff_cat($parent);
    echo "</td></tr>
    <tr><td bgcolor='#ffffcc' colspan='2' align='center'><input type='submit' value='Modifier'></td></tr></table></form>";
  }
}
if (isset($_GET['up'])){
    $req = $sql->doQuery('SELECT place FROM menu WHERE id = ' . $_GET['id'] . ' AND parent LIKE ' . $parent);
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
    $sql_up = $sql->doQuery('UPDATE menu SET place = ' . $place . ' WHERE parent = ' . $parent . ' AND place = ' . ($place  - 1));
    $sql_up = $sql->doQuery('UPDATE menu SET place = ' . ($place - 1) . ' WHERE parent = ' . $parent . ' AND id =  ' . $_GET['id']);
    echo $sql->error();
  }
  if (isset($_GET['down'])){
    $req = $sql->doQuery('SELECT place FROM menu WHERE parent = ' . $parent . ' AND id = "' . $_GET['id'] . '"');
	  $tmp = $sql->fetch($req);
	  $place = (isset($tmp->place) AND !empty($tmp->place))?$tmp->place:1;
    $sql_up = $sql->doQuery('UPDATE menu SET place = ' . $place . ' WHERE parent = ' . $parent . ' AND place = ' . ($place + 1));
    $sql_up = $sql->doQuery('UPDATE menu SET place = ' . ($place + 1) . ' WHERE parent = ' . $parent . ' AND id =  ' . $_GET['id']);
    echo $sql->error();

  }
echo "<a href ='" . ADMIN_NOM_PAGE . "parent=0'>Retour</a>&nbsp;<a href ='" . ADMIN_NOM_PAGE_MENU . "add=1'>Ajouter</a>&nbsp;<form action='", ADMIN_NOM_PAGE, "' method='POST'>";
aff_cat($parent);
echo "<input type='submit' value='Aller à'></form><hr />";
$menu = $sql->doQuery('SELECT * FROM menu WHERE parent LIKE ' . $parent . ' ORDER BY place');
echo '
<script language="javascript">
<!--
function supprimer(id){
  if (confirm("Etes vous sur de supprimer cette éléments ?")){
    window.location = "' . ADMIN_NOM_PAGE_MENU . 'supp=1&id=" + id;
  }
}
-->
</script>';
if ($sql->numrow($menu) > 0)
{
  echo "<table border='1' bordercolor='#339933' cellspacing='0'><tr bgcolor='#339933'><th>Place</th><th>Titre</th><th>lien</th><th>Description</th><th>Action</th></tr>";
  $first = True;
  $lastid = 0;
  while($row = $sql->fetch($menu)){

    if (!$first)
      echo "<a href='", ADMIN_NOM_PAGE_MENU, "down=1&id=", $lastid, "'><img src='./images/down.gif'  border='0' width='17' height='17' ></a></td></tr>";
	  echo "<tr bgcolor='#ffffcc'><td>", $row->place, "</td><td><img src='./images/repertoire.gif'  border='0' width='17' height='17' >&nbsp;
        <a href='", ADMIN_NOM_PAGE, "parent=", $row->id, "'>", deprotege_texte($row->titre), "</a></td><td>", deprotege_texte($row->url), "</td><td>", ($row->description!='')?deprotege_texte($row->description):'&nbsp;', "</td><td>
        <a href='javascript:supprimer(", $row->id, ");'><img src='./images/supprimer.gif'  border='0' width='17' height='17' ></a>
        &nbsp;&nbsp;<a href='", ADMIN_NOM_PAGE_MENU, "mod=1&id=", $row->id, "'><img src='./images/editer.gif'  border='0' width='17' height='17' ></a>
        &nbsp;&nbsp;<a href='", ADMIN_NOM_PAGE_MENU, "mov=1&id=", $row->id, "'><img src='./images/deplacer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;",
        (!$first?"<a href='". ADMIN_NOM_PAGE_MENU . "up=1&id=" . $row->id . "'><img src='./images/up.gif'  border='0' width='17' height='17' ></a>":"<img src='./images/spacer.gif'  border='0' width='17' height='17' >");
	  $first = False;
	  $lastid = $row->id;
  } // while
echo "</td></tr></table>";
}
else
  echo 'Il n\'y pas d\'élement de sous-menu pour ce menu <br /><a href="'. ADMIN_NOM_PAGE. '">retour</a>';
?>
