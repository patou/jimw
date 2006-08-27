<?php

/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
 * @copyright 2005
 **/
 
function aff_select($tableau, $select = 0, $parent = 0, $prof = 0)
{
  if (isset($tableau[$parent]))
  {
    for($deca='',$i=0;$i<=$prof;$i++)
      $deca .= '&nbsp;>&nbsp;';
    while(list($id, $titre) = each($tableau[$parent]))
    {
    			echo '<option value="', $id, '" ', (($id == $select)?'selected':''), '>', $deca, ' ', $titre, '</option>';
			aff_select($tableau, $select, $id, $prof+1);
    }

  }
} 

function aff_xml($tableau, $select = 0, $parent = 0, $prof = 0)
{
  if (isset($tableau[$parent]))
  {
    while(list($id, list($titre, $statut)) = each($tableau[$parent]))
    {
    		echo '<item id="', $id, '" ', (($id == $select)?'select="yes"':''),' text="', utf8_encode(deprotege_texte($titre)), '" open="1">
			<userdata name="statut">', $statut,'</userdata>';
			aff_xml($tableau, $select, $id, $prof+1);
			echo '</item>';
    }

  }
}

function creer_tab_arbo($req)
{
 global $sql;
 $query_cat = $sql->doQuery($req);
  $h = array();
  $i = 0;
  while ($Enr=$sql->fetch($query_cat))
	{
	   if (isset($Enr->statut))
       	  $h[$Enr->parent][$Enr->id] = array($Enr->titre, $Enr->statut);
 	   else
 	   	  $h[$Enr->parent][$Enr->id] = $Enr->titre; 
	   $i++;
	}
	return $h;
}

if (isset($_GET['xml']))
{
	//$id = (isset($_GET['id']))?$_GET['id']:0;
	header('Content-type:text/xml');
	echo '<?xml version="1.0"?'.'>';
	$racine = PAGE_ROOT . REP_ARTICLE;
	$fp = opendir($racine);
	echo '<tree id="0">';
	aff_xml(creer_tab_arbo("SELECT id, parent, titre, statut FROM article"));
	echo "</tree>";
	exit();
} 
if (isset($_GET['sup'])) 
{
	$req_his = $sql->doQuery('SELECT id FROM pages WHERE article LIKE "'. $_GET['id'] .'";');
	while($res = $sql->fetch($req_his))
	{
       @unlink(PAGE_ROOT . REP_ARTICLE . $res->id . '.htm');
       $req_sup = $sql->doQuery('DELETE FROM pages WHERE id LIKE "' . $res->id . '"', $sql);
	}
	$req_sup = $sql->doQuery('DELETE FROM article WHERE id LIKE "' . $_GET['id'] . '"', $sql);
	echo "Supression reussis !<br />";
}
if (isset($_GET['hsup'])) 
{
    if ($_GET['der'] == 1)
	   $res_der = $sql->doQuery('UPDATE pages SET dernier = "1" WHERE article LIKE "' . $_GET['id_art'] . '" LIMIT 1;');
	$req_sup = $sql->doQuery('DELETE FROM pages WHERE id LIKE "' . $_GET['id'] . '"', $sql);
    @unlink(PAGE_ROOT . REP_ARTICLE . $_GET['id'] . '.htm');
	echo "Supression reussis !<br />";
}
if (isset($_POST['add'])) {
	$req_max_art = $sql->doQuery('SELECT max(id) as max FROM article');
	$req_max = $sql->doQuery('SELECT max(id) as max FROM pages');
	$max_art = $sql->fetch($req_max_art);
	$max = $sql->fetch($req_max);
	$id = $max->max + 1;
	$id_art = $max_art->max + 1;
	$req_page = $sql->doQuery('INSERT INTO pages (id, article, dernier, date, commentaire) 
	VALUES ("' . $id . '", "' . $id_art . '", "1", "' . date('Y-m-d') . '", "Création de la page");');
	$rubrique = (isset($_POST['rubrique']))?$_POST['rubrique']:0;
	$parent = (isset($_POST['parent']))?$_POST['parent']:0;
	$statut = (isset($_POST['statut']))?$_POST['statut']:0;
	$req_page = $sql->doQuery('INSERT INTO article (id, rubrique, parent, statut, titre, date) 
	VALUES ("' . $id_art . '", "' . $rubrique . '", "' . $parent . '", "' . $statut . '", "' . protege_texte($_POST['titre']) . '", "' . date('Y-m-d') . '");');
    $fp = fopen (PAGE_ROOT . REP_ARTICLE . $id . ".htm", "w+");
    fwrite ($fp, stripslashes($_POST['file']));
    fclose ($fp);
	echo "Ajout reussis !<br />";
}
if (isset($_POST['mod'])) {
	$req_max = $sql->doQuery('SELECT max(id) as max FROM pages');
	$max = $sql->fetch($req_max);
	$id = $max->max + 1;
	$req_dern = $sql->doQuery('UPDATE pages SET dernier = "0" WHERE article LIKE "' . $_POST['id_art'] . '";');
	$req_page = $sql->doQuery('INSERT INTO pages (id, article, dernier, date, commentaire) 
	VALUES ("' . $id . '", "' . $_POST['id_art'] . '", "1", "' . date('Y-m-d') . '", "' . protege_texte($_POST['commentaire']) . '");');
	$rubrique = (isset($_POST['rubrique']))?$_POST['rubrique']:0;
	$parent = (isset($_POST['parent']))?$_POST['parent']:0;
	$statut = (isset($_POST['statut']))?$_POST['statut']:0;
	$req_page = $sql->doQuery('UPDATE article SET rubrique = "' . $rubrique . '",  parent = "' . $parent . '", statut ="' . $statut . '", 
	titre = "' . protege_texte($_POST['titre']) . '" WHERE id LIKE "' . $_POST['id_art'] . '";');
    $fp = fopen (PAGE_ROOT . REP_ARTICLE . $id . ".htm", "w+");
    fwrite ($fp, stripslashes($_POST['file']));
    fclose ($fp);
	echo "Modification reussis !<br />";
}
if (isset($_POST['sauv'])) {
	$parent = (isset($_POST['parent']))?$_POST['parent']:0;
	$statut = (isset($_POST['statut']))?$_POST['statut']:0;
	$req_page = $sql->doQuery('UPDATE article SET parent = "' . $parent . '", statut ="' . $statut . '", 
	titre = "' . protege_texte($_POST['titre']) . '" WHERE id LIKE "' . $_POST['id'] . '";');
    echo "Enregistrement reussis !<br />";
}
if (isset($_GET['der']))
{
   $req_dern = $sql->doQuery('UPDATE pages SET dernier = "0" WHERE article LIKE "' . $_GET['id_art'] . '";');
   $req = $sql->doQuery('UPDATE pages SET dernier = "1" WHERE id LIKE "' . $_GET['id'] . '";');
}
if (isset($_GET["add"])) {
	echo '<script type="text/javascript" src="', URL_ROOT , REP_EDITOR , 'FCKeditor', '/fckeditor.js"></script>
	<script type="text/javascript">
window.onload = function()
{
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = "/fckeditor/" ;	// "/fckeditor/" is the default value.
	var oFCKeditor = new FCKeditor( "file" ) ;
	oFCKeditor.BasePath	= "', URL_ROOT . REP_EDITOR . 'FCKeditor/' . '" ;
	oFCKeditor.Width = 600 ;
	oFCKeditor.Height = 400 ;
	oFCKeditor.ReplaceTextarea() ;
}
function changeStatut(s)
{
 	var img = document.getElementById("statut_img");
	img.src = "./images/statut" + s.options[s.selectedIndex].value + ".gif"; 	  
}

</script>
';
  echo '<form action="', ADMIN_NOM_PAGE, '" method="POST">
	<input type="hidden" name="add" value="1"><table width="750" valign="top" align="center" border="1" bordercolor="#339933" cellspacing="0">
      <tr>
      <td colspan="2" bgcolor="#339933">
        <div align="center"><H3>Modifier une page </H3></div>
      </td>
    </tr>
    <tr><td bgcolor="#ffffcc" width="100">Titre : </td><td><input type="text" name="titre" value="" size="80"></td></tr>
    <tr><td bgcolor="#ffffcc" width="100">Catégorie : </td><td>
	<input type="checkbox" name="rubrique" value="1" checked>Cocher cette case si vous voulez que cette article soit une catégorie<br />
	<select name="parent">
	<option value="0" selected>Racine</option>';
	aff_select(creer_tab_arbo("SELECT id, parent, titre FROM article WHERE rubrique LIKE '1'"));
	echo "</select>
	<tr><td bgcolor='#ffffcc' width='100'>Texte :</td><td><textarea id='file' name='file' style='width:600;height:350'>";
	echo '</textarea></td><td>
    <tr><td bgcolor="#ffffcc" width="100">Statut : </td><td>
	<img src="./images/statut0.gif" id="statut_img" border="0" width="17" height="17">
	<select name="statut" value="" onChange="changeStatut(this);">
	<option value="0" selected>Redaction</option>
	<option value="1">Correction</option>
	<option value="2">Publier</option>
	<option value="3">Supprimer</option>
	</select></td></tr>
	<tr><td bgcolor="#ffffcc" colspan="2" align="center"><input type="submit" value="Créer"></td></tr></table></form>';
}

if (isset($_GET['mod'])){
   $req = $sql->doQuery('SELECT p.id, a.rubrique , a.parent , a.statut , a.titre 
FROM article AS a, pages AS p
WHERE a.id = p.article AND a.id = '. $_GET['id'] .' AND p.dernier = 1 LIMIT 1;');
   $res = $sql->fetch($req);
    echo '<script type="text/javascript" src="', URL_ROOT , REP_EDITOR , 'FCKeditor', '/fckeditor.js"></script>
	<script type="text/javascript">
window.onload = function()
{
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = "/fckeditor/" ;	// "/fckeditor/" is the default value.
	var oFCKeditor = new FCKeditor( "file" ) ;
	oFCKeditor.BasePath	= "', URL_ROOT . REP_EDITOR . 'FCKeditor/' . '" ;
	oFCKeditor.Width = 600;
	oFCKeditor.Height = 400;
	oFCKeditor.ReplaceTextarea();
}
function changeStatut(s)
{
 	var img = document.getElementById("statut_img");
	img.src = "./images/statut" + s.options[s.selectedIndex].value + ".gif"; 	  
}

</script>';
  echo '<form action="', ADMIN_NOM_PAGE, '" method="POST"><input type="hidden" name="id_art" value="', $_GET['id'],'">
	<input type="hidden" name="mod" value="1"><table width="750" valign="top" align="center" border="1" bordercolor="#339933" cellspacing="0">
      <tr>
      <td colspan="2" bgcolor="#339933">
        <div align="center"><H3>Modifier une page </H3></div>
      </td>
    </tr>
    <tr><td bgcolor="#ffffcc" width="100">Titre : </td><td><input type="text" name="titre" value="', deprotege_texte($res->titre), '" size="80"></td></tr>
    <tr><td bgcolor="#ffffcc" width="100">Catégorie : </td><td>
	<input type="checkbox" name="rubrique" value="1" ', ($res->rubrique==1)?'checked':'','>Cocher cette case si vous voulez que cette article soit une catégorie<br />
	<select name="parent">
	<option value="0" ', ($res->parent==0)?'selected':'', '>Racine</option>';
	aff_select(creer_tab_arbo("SELECT id, parent, titre FROM article WHERE rubrique LIKE '1'"), $res->parent);
	echo "</select>
	<tr><td bgcolor='#ffffcc' width='100'>Texte :</td><td><textarea id='file' name='file' style='width:600;height:350'>";
	readfile(PAGE_ROOT . REP_ARTICLE . $res->id . '.htm');
    echo '</textarea></td><td>
    <tr><td bgcolor="#ffffcc" width="100">Statut : </td><td>
	<img src="./images/statut', $res->statut,'.gif" id="statut_img" border="0" width="17" height="17">
	<select name="statut" value="" onChange="changeStatut(this);">
	<option value="0" ', ($res->statut==0)?'selected':'','>Redaction</option>
	<option value="1" ', ($res->statut==1)?'selected':'','>Correction</option>
	<option value="2"', ($res->statut==2)?'selected':'','>Publier</option>
	<option value="3" ', ($res->statut==3)?'selected':'','>Supprimer</option>
	</select></td></tr>
	<tr><td bgcolor="#ffffcc" width="100">Commentaire : </td><td><input type="text" name="commentaire" value="" size="80"></td></tr>
	<tr><td bgcolor="#ffffcc" colspan="2" align="center"><input type="submit" value="Modifier"></td></tr></table></form>';
}
if (isset($_GET['his']))
{
  $req = $sql->doQuery('SELECT a.id, a.rubrique , a.parent , a.statut , a.titre, a.date 
FROM article AS a
WHERE a.id = '. $_GET['id_art'] .' LIMIT 1;');
   $res = $sql->fetch($req);    
   echo '<h3>', $res->titre, '</h3>
   	<script type="text/javascript">
function afficher()
{
 	document.getElementById("preview").style.display = "block";
    //document.getElementById("fpreview").style.display = "block";
	return true;	 	  
}
function supprimer_his(id, der){
  if (confirm("Etes vous sur de supprimer cette éléments ?"))
  {
    var form = document.forms["detailsform"];		 
    window.location = "' . ADMIN_NOM_PAGE . 'his&hsup&id_art=', $res->id, '&id=" + id + "&der=" + der;
  }
}
</script>
   <div style="display:none" id="preview"><a href="', PAGE_ROOT , REP_ARTICLE , $res->id, '.htm" target="fpreview" onClick="document.getElementById(\'preview\').style.display = \'none\';">Cacher</a><br /><iframe name="fpreview" style="width:600px;height:300px" ></iframe></div>';
   $req_his = $sql->doQuery('SELECT id, commentaire , date , dernier 
FROM pages
WHERE article = '. $_GET['id_art'] .';');
echo '<br /><table border="1" bordercolor="#339933" cellspacing="0" width="600">
<tr bgcolor="#339933"><th>Date</th><th>Commentaire</th><th>Action</th></tr>';
$num = $sql->numRow($req_his);
while($res_his = $sql->fetch($req_his))
{	
	echo '<tr bgcolor="#ffffcc"><td>', $res_his->date, '</td><td>', $res_his->commentaire, '</td><td>';
    if ($num != 1)
	   echo '<a href="javascript:supprimer_his(\'', $res_his->id, '\', \'', $res_his->dernier, '\');"><img src="./images/supprimer.gif"  border="0" width="17" height="17" ></a>';
	echo '&nbsp;&nbsp;<a href="', PAGE_ROOT , REP_ARTICLE , $res_his->id, '.htm" target="fpreview" onClick="afficher();"><img src="./images/editer.gif"  border="0" width="17" height="17" ></a>';
	if (!$res_his->dernier)
	  echo '&nbsp;&nbsp;<a href="' . ADMIN_NOM_PAGE . 'his&der&id=', $res_his->id, '&id_art=', $res->id, '"><img src="./images/up.gif"  border="0" width="17" height="17" ></a>';
	echo '</td></tr>';
}
echo '</table>'; 
}

//Afichage du menu


echo '<a href ="' . ADMIN_NOM_PAGE . 'add">Ajouter</a><br/>';
echo '
<script language="javascript">
<!--
function supprimer(){
  if (confirm("Etes vous sur de supprimer cette éléments ?"))
  {
    var form = document.forms["detailsform"];		 
    window.location = "' . ADMIN_NOM_PAGE . 'sup&id=" + form.id.value;
  }
}
function modifier(){
  var form = document.forms["detailsform"];		 
  window.location = "' . ADMIN_NOM_PAGE . 'mod&id=" + form.id.value;
}

function historique(){
  var form = document.forms["detailsform"];		 
  window.location = "' . ADMIN_NOM_PAGE . 'his&id_art=" + form.id.value;
}
function changeStatut(s)
{
 	var img = document.getElementById("statut_img");
	img.src = "./images/statut" + s.options[s.selectedIndex].value + ".gif"; 	  
}
-->
</script>
<style>
.standartTreeRow{	font-family : Verdana, Geneva, Arial, Helvetica, sans-serif; 	font-size : 12px; -moz-user-select: none; }
.selectedTreeRow{ background-color : navy; color:white; font-family : Verdana, Geneva, Arial, Helvetica, sans-serif; 		font-size : 12px;  -moz-user-select: none;  }
.standartTreeImage{ width:18px; height:18px;  overflow:hidden; border:0; padding:0; margin:0; }
</style>
<div id="select" style="display:none">
<form name="detailsform" method="POST" action="', ADMIN_NOM_PAGE, '">
<input type="hidden" name="id"><input type="hidden" name="sauv" value="1">
<img src="./images/statut0.gif" id="statut_img" border="0" width="17" height="17">
<select name="statut" value="" onChange="changeStatut(this);">
	<option value="0" selected>Redaction</option>
	<option value="1">Correction</option>
	<option value="2">Publier</option>
	<option value="3">Supprimer</option>
</select>
<img src="./images/deplacer.gif" name="add" border="0" width="17" height="17">
<select name="parent">
<option value="0" selected>Racine</option>';
aff_select(creer_tab_arbo("SELECT id, parent, titre FROM article WHERE rubrique LIKE '1'"));
echo '</select>
<br />
<input type="text" name="titre"><input type="submit" value="sauvegarder"><br />
<a href= "javascript:supprimer();" title="Supprimer"><img src="./images/supprimer.gif" name="add" border="0" width="17" height="17"></a>
<a href= "javascript:modifier();" title="Modifier"><img src="./images/editer.gif" name="mod" value="1" border="0" width="17" height="17"></a>
<a href= "javascript:historique();" title="Historique"><img src="./images/historique.gif" name="mod" value="1" border="0" width="17" height="17"></a>
</form>
</div>
<a href="javascript:void(0);" onclick="tree.openAllItems(0);">Ouvrir</a>
<a href="javascript:void(0);" onclick="tree.closeAllItems(0);">Fermer</a><br />
<div id="treeBox" style="width:200"></div> 
<script type="text/javascript" src="', PAGE_ROOT , REP_EDITOR , 'dhtmlXCommon.js"></script>
<script type="text/javascript" src="', PAGE_ROOT , REP_EDITOR , 'dhtmlXTree.js"></script>
<script> 
 function Selectparent(id)
 {
    var op = document.forms["detailsform"].elements["parent"];
	for(var i = 0; i < op.options.length; i++)
	{
	 	 if (op.options[i].value == id)
	 	 	op.options[i].selected = true;
 	 	 else
		   	op.options[i].selected = false; 
    } 
 }
 function Selectstatut(id)
 {
    var op = document.forms["detailsform"].elements["statut"];
	for(var i = 0; i < op.options.length; i++)
	{
	 	 if (op.options[i].value == id)
	 	 	op.options[i].selected = true;
 	 	 else
		   	op.options[i].selected = false; 
    } 
    changeStatut(op);
 }
 function doOnSelect(id)
 {
  		 var div = document.getElementById("select");
		 div.style.display = "block";
		 var f = document.forms["detailsform"];
		 f.elements["id"].value = id;
		 f.elements["titre"].value = tree.getItemText(id);
		 Selectparent(tree.getParentId(id)); 
		 Selectstatut(tree.getUserData(id,"statut"));
 }
 var tree = new dhtmlXTreeObject(document.getElementById("treeBox"),"100%","100%",0);
 tree.setImagePath("./images/"); 
 tree.enableCheckBoxes(false); 
 tree.enableDragAndDrop(false);
 tree.setOnClickHandler(doOnSelect);
 tree.loadXML("' . ADMIN_NOM_PAGE . 'echo_off&xml");
</script><hr color=red />';

?>