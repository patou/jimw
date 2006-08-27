<?php

/**
* Site du jeu Risk Star Wars
* 	module lien
* 
* @version $Id: module.php,v 1.1 2006/05/22 12:03:07 lesgrumels Exp $
* @copyright 2005
*/

$temp->set_file("lien" , "lien.htm"); //definie une page pour le site !
$temp->set_var("PRESENTATION" , "Voici des liens int&eacute;ressant");
//affichage des rubriques !

$sql_lien = 'SELECT cat_lien . titre AS cat , lien . url AS url , lien . titre AS titre, lien . description AS description
FROM cat_lien , lien
WHERE lien . cat = cat_lien . id
ORDER BY cat_lien . place ASC';
$result = $sql->doQuery($sql_lien);
echo $sql->Error();
$temp->set_block("lien", "CAT_LIEN", "cat_lien");
$temp->set_var("cat_lien", "");
$temp->set_block("lien", "LIENS");
$first = TRUE;
$cat = '';
while($row = $sql->fetch($result)){
	if ($cat <> $row->cat) { //nouvelle categorie
	    if (!$first) $temp->parse("cat_lien", "CAT_LIEN", true);
		else $first = FALSE;
		$cat = $row->cat;
	    $temp->set_var("CAT_TITRE", $cat );
		$temp->set_var("CAT_LIEN_HTML", '' );
	}
	$temp->set_var(array("LIEN_LIEN" => $row->url, "LIEN_NOM" => $row->titre, "LIEN_DESC" => $row->description));
	$temp->parse("CAT_LIEN_HTML", "LIENS", true);
	
} 
$temp->parse("cat_lien", "CAT_LIEN", true);
$temp->set_var("LIENS", '' );
$temp->parse("CONTENU", "lien");

?>
