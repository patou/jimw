<?php

/**
* Site du jeu Risk Star Wars
* 	module index
*
* @version $Id: module.php,v 1.1 2006/05/22 12:02:59 lesgrumels Exp $
* @copyright 2005
*/
$temp->set_file("index" , "index.htm"); //definie une page pour le site !
$temp->set_var("PRESENTATION" , $conf->get("presentation"));
//affichage des news
$nb_par_page = 5;
if (empty($_GET['debut'])) $debut=0;
else $debut=$_GET['debut'];
if ($debut<0) $debut=0;
$fin=$debut+$nb_par_page;
$query = "SELECT titre, date, text FROM news ORDER BY date DESC LIMIT $debut, $nb_par_page"; // WHERE date <= '".date("dmY")."'
//echo $query;
$news = $sql->doQuery ($query); //WHERE date <= ".date("Y-m-d")." ORDER BY date DESC LIMIT $debut, $nb_par_page
$news_nb = $sql->doQuery ("SELECT id FROM news", $sql);
$nb_news = $sql->numRow($news_nb);
$temp->set_block("index", "NEWS", "news");
$temp->set_var("news", "");
while($row = $sql->fetch($news)){
	//$date = explode("/",$row->date);
	$temp->set_var(array("NEWS_TITRE" => $row->titre, "NEWS_DATE" => $row->date, "NEWS_TEXTE" => $row->text));
	$temp->parse("news", "NEWS", true);
} // while
if($debut!=0)
	{
	 $pre = $debut-$nb_par_page;
     $temp->set_block("index", "NEWS_PRECEDENT_LIEN" );
     $temp->set_var(array("NEWS_LIEN_PRECEDENT" => "?debut=".$pre,
     "NEWS_PRECEDENT_TEXT" => "Pr&eacute;c&eacute;dent"));
     $temp->set_block_not("index", "NEWS_PRECEDENT" );
   	}
else	{
	$pre=0;
 	$temp->set_block("index", "NEWS_PRECEDENT" );
     $temp->set_var(array("NEWS_PRECEDENT" => "Pr&eacute;c&eacute;dent"));
     $temp->set_block_not("index", "NEWS_PRECEDENT_LIEN" );
	}

if ($debut+$nb_par_page<=$nb_news)
	{
	$sui=$debut+$nb_par_page;
     $temp->set_block("photo", "NEWS_SUIVANT_LIEN" );
     $temp->set_var(array("NEWS_LIEN_SUIVANT" => "?debut=".$sui,
     "NEWS_SUIVANT_TEXT" => "Suivant"));
     $temp->set_block_not("photo", "NEWS_SUIVANT" );
	}
else
	{
	 $temp->set_block("photo", "NEWS_SUIVANT" );
     $temp->set_var(array("NEWS_SUIVANT" => "Suivant"));
     $temp->set_block_not("photo", "NEWS_SUIVANT_LIEN" );

	}
$temp->parse("CONTENU", "index");

?>
