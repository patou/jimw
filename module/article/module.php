<?php
/**
* Site du jeu Risk Star Wars
* 	module index
*
* @version $Id: module.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
$temp->set_file(array(
     "article" => "article.htm"));

if (isset($_GET["nom"])) {
   $req = $sql->doQuery('SELECT p.id, a.rubrique , a.parent , a.statut , a.titre 
FROM article AS a, pages AS p
WHERE a.id = p.article AND a.id LIKE "'. $_GET['nom'] .'" AND p.dernier = 1 LIMIT 1;');
    $res = $sql->fetch($req);
    if (!$res->id AND !file_exists(PAGE_ROOT . REP_ARTICLE . $res->id . ".htm"))
    {
		$temp->set_block_not('article', 'LIST_ARTICLE');
	    $temp->set_block('article', 'NO_ARTICLE');
	    $temp->set_var('ARTICLE_NOARTICLE', 'Cette article n\'existe pas !');
		$temp->parse("CONTENU", "article"); //definie une page pour le site !         
    }
    else
    {
        $temp->set_block_not('article', 'NO_ARTICLE');
	    $titre = $res->titre;
//	    if ($res->rubrique)
	//    {
			$query_cat = $sql->doQuery('SELECT id, parent, titre FROM article WHERE statut LIKE "2";');
			$tab_cat = array();
			if ($res->rubrique) 
			   $temp->set_block('article', 'LIST_ARTICLE', 'list_article');
			while ($Enr=$sql->fetch($query_cat))
			{
			  $tab_cat[$Enr->id] = array($Enr->parent, $Enr->titre);
			  if ($res->rubrique && $Enr->parent == $_GET["nom"])
			  {
			    $temp->set_var(array(
			     'ID_ARTICLE' => $Enr->id,
			     'NOM_ARTICLE' => stripslashes($Enr->titre)
			     ));
			    $temp->parse('list_article', 'LIST_ARTICLE',true);
			  }
			}
			$chemin_id = $_GET["nom"];
			$chemin = '';
			while ($chemin_id != 0)
			{
			    $chemin = '<a href="' . $_SERVER['PHP_SELF'] . '?nom=' . $chemin_id . '" >' . stripslashes($tab_cat[$chemin_id][1]) . '</a>&nbsp;&gt;&nbsp;' . $chemin;
			    $chemin_id = $tab_cat[$chemin_id][0];
			}
			$temp->set_var('CHEMIN_ARTICLE', $chemin );
	
	    if (!$res->rubrique)
	    {
	     	$temp->set_block_not('article', 'IS_RUBRIQUE');
    	}
	   	$temp->set_file("contenu_article" , "../../" . REP_ARTICLE . $res->id . ".htm");
	   	$temp->parse("CONTENU_ARTICLE", "contenu_article");
	   	$temp->set_var(array(
				 'TITRE_ARTICLE' => $res->titre
				 ));
		$temp->parse("CONTENU", "article"); //definie une page pour le site !
    }
}
else{ //Liste les articles
    $temp->set_block_not('article', 'LIST_ARTICLE');
    $temp->set_block('article', 'NO_ARTICLE');
    $temp->set_var('ARTICLE_NOARTICLE', 'Cette article n\'existe pas !');
	$temp->parse("CONTENU", "article"); //definie une page pour le site !
}
?>