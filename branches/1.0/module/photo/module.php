<?
define("L", 200);
define ("HMAXI", 320);
$v_aff = 3;
$h_aff = 12;
$LIEN_PHOTO_SRC = PAGE_ROOT . REP_PHOTO;
$temp->set_file(array(
     "photo" => "photo.htm"));
//Recuperation des variables :
$debut = (!isset($_GET['debut']))?0:$_GET['debut'];
$album = (!isset($_GET['album']))?0:$_GET['album'];
//Affichage des catégorises
$temp->set_block('photo', 'LIST_ALBUM', 'list_album');
$temp->set_var('list_album', '');
$query_cat = $sql->doQuery("SELECT id, parent, titre, nb FROM cat_photo");
echo $sql->Error();
$tab_cat = array();
while ($Enr=$sql->fetch($query_cat))
{
  $tab_cat[$Enr->id] = array($Enr->parent, $Enr->titre, $Enr->nb);
  if ($Enr->parent == $album)
  {
    $temp->set_var(array(
     'ID_ALBUM' => $Enr->id,
     'SELECT_ALBUM' => '',
     'NOM_ALBUM' =>$Enr->titre . (($Enr->nb!=0)?' ( '.$Enr->nb.' ) ':'')
     ));
    $temp->parse('list_album', 'LIST_ALBUM',true);
  }
}

if ($album > 0)
{
  $nb_images = $tab_cat[$album][2];
  $nom_album = $tab_cat[$album][1];
}
else
{
  $nb_images = 0;
  $nom_album = $titre;
}
$chemin_id = $album;
$chemin = '';
while ($chemin_id != 0)
{
    $chemin = '<a href="' . $_SERVER['PHP_SELF'] . '?album=' . $chemin_id . '" >' . $tab_cat[$chemin_id][1] . '</a>&nbsp;&gt;&nbsp;' . $chemin;
    $chemin_id = $tab_cat[$chemin_id][0];
}
$temp->set_var('CHEMIN_ALBUM', '<a href="' . $_SERVER['PHP_SELF'] . '?album=0" >Photos</a>&nbsp;&gt;&nbsp;' . $chemin );
$temp->set_var(array('PHOTO_ORDER_ID' => 'Id',
     'PHOTO_ORDER_NOM' => 'nom',
     'PHOTO_ORDER_TITRE' => 'titre',
     'TITRE_ALBUM' => $nom_album));     //les variables de classements
$url1="&debut=$debut&album=$album";  //pour ajouter les arguments (critère de recherche) pour le passage aux autres page comme classements et pages suivantes
$url2="&album=$album";
if (isset($_GET['mots'])){$url1.= '&mots=' . $_GET['mots']; $url2.= '&mots=' . $_GET['mots'];}
if (isset($_GET['date_img'])){$url1.= '&date_img=' . $_GET['date_img']; $url2.='&date_img=' . $_GET['$date_img'];}
if (isset($_GET['order'])) $url2.= '&order=' . $_GET['order'];
if (isset($_GET['ad'])) $url2.= '&ad=' . $_GET['ad'];
$temp->set_var(array("PHOTO_URL1" => $url1,
     "PHOTO_URL2" => $url2));
//construction de la requette
if ($nb_images == 0) {
   $temp->set_block_not("photo", "NO_PHOTO_BLOC");   //affiche message quand pas de photos
      $temp->set_var(array("PHOTOS_NOPHOTOS" => 'Il n\'y a pas de photos dans cette album ',
                      'PHOTO_URL1' => '&album='.$album));
  if (SQL_TYPE == 'mysql')
    $requette="SELECT p.id, p.titre as description, c.titre, p.rubrique  FROM photos as p, cat_photo as c WHERE p.rubrique LIKE c.id ORDER BY rand() LIMIT 6"; //affichage de photos aléatoire
  else
    {
      return;
    }
  $page_spe = TRUE;
}
else {

$page_spe = FALSE;
$requet="SELECT * FROM photos WHERE rubrique LIKE '$album' AND  ";
if (isset($_GET['mots'])) $requet.="photos.description LIKE '%$mots%' AND  ";
if (isset($_GET['date_img']))$requet.="photos.date LIKE '$date_img' AND  ";
$requette= substr ($requet,0, strlen($requet)-6);
$order = (!isset($_GET['order']))?'id':$_GET['order'];
$ad = (!isset($_GET['ad']))?'ASC':$_GET['ad'];
$requette .= " ORDER BY $order $ad limit $debut,$h_aff";
}
$query = $sql->doQuery($requette);
$i=0;
$lignes = $nb_images;
//$temp->set_block_not("photo", "NO_PHOTO"); //enleve l'affichage  quand il n'y as pas de photos
$temp->set_block("photo", "PHOTO", "photo_affi"); //bloc d'affichages des photos  *
$temp->set_block("photo", "PHOTO_LIGNE_B", "PHOTO_LIGNE_B"); //bloc a afficher pour finir une ligne et encommencer une autre pour afficher plusieur photos sur une seul ligne
$temp->set_var("photo_affi" , '');
$fin_ligne = $temp->get("PHOTO_LIGNE_B");
while ($Enr=$sql->fetch($query))
{
$i++;
$mod = $i % $v_aff;   //calcule du nombre de photos sur une ligne
$size = @getimagesize($LIEN_PHOTO_SRC.$Enr->id.'.jpg');
$l=$size[0];
    $h=$size[1];
    $largeur=$l;
    $hauteur= $h ;
    if ($hauteur > HMAXI ){
        $largeur = floor($largeur * HMAXI /$hauteur);
        $hauteur=HMAXI;
    }
    if ($largeur > L  ){
        $largeur=L;
    $hauteur= floor($largeur * $h / $l);
}
$width=$largeur;
$height=$hauteur;
$temp->set_var(array('PHOTO_LARGEUR' => $page_spe?$width/2:$width,
     'PHOTO_HAUTEUR' => $page_spe?$height/2:$height,
     'PHOTO_TITRE' => $page_spe?'<a href="photo.php?album=' . $Enr->rubrique . '" style="text-decoration:none"><img src="./images/repertoire.gif" border=0>' . $Enr->titre . '</a>':$Enr->titre,
     'PHOTO_DESCRIPTION' => $Enr->description,
     'PHOTO_ID' => $Enr->id,
     'PHOTO_LIEN_IMAGE' => (file_exists($LIEN_PHOTO_SRC.'mini/'.$Enr->id.'.jpg'))?$LIEN_PHOTO_SRC.'mini/'.$Enr->id.'.jpg':$LIEN_PHOTO_SRC.$Enr->id.'.jpg',
     'PHOTO_LIEN' => $page_spe?$_SERVER['PHP_SELF'].'?album='.$Enr->rubrique:$LIEN_PHOTO_SRC.$Enr->id.'.jpg',
     'PHOTO_FIN_LIGNE' => ($mod==0)?$fin_ligne:'')); //affichage des variables
/*   if (isset($vote_actif)){       //liens pour voter si definer dans site(numero du site).php
   if (empty($photo_vote_one))  {
$temp->set_block("PHOTO", "PHOTO_VOTE_B");
$temp->set_var(array("PHOTO_VOTE" => 'vote',
"PHOTO_VOTE_ID" => $Enr->id));
$photo_vote_one = 1;
}
else {
$temp->subst("PHOTO_VOTE_B");
$temp->set_var(array("PHOTO_VOTE" => 'vote',
"PHOTO_VOTE_ID" => $Enr->id));
    }
//echo "<a href='#' onClick=window.open('vote.php?Enr={PHOTO_ID}','','scrollbars=yes,width=400,height=150,left=50,top=50')><font face=$font_face size=$font_size>{PHOTO_VOTE}</font></a><br>";
}
else{ // $vote_actif==TRUE
$temp->set_block_not("PHOTO", "PHOTO_VOTE_B");
}
if (isset($comment_actif)){       //liens pour commentaire si definer dans site(numero du site).php
   if (empty($photo_comment_one))  {
$temp->set_block("PHOTO", "PHOTO_COMMENT_B");
$temp->set_var(array("PHOTO_COMMENT" => $Enr->comment,
"PHOTO_COMMENT_ID" => $Enr->id,
"PHOTO_COMMENT_TEXT" => $LANG_PHOTO_COMMENT));
$photo_comment_one = 1;
}
else {
$temp->subst("PHOTO_COMMENT_B");
$temp->set_var(array("PHOTO_COMMENT" => $Enr->comment,
"PHOTO_COMMENT_ID" => $Enr->id,
"PHOTO_COMMENT_TEXT" => $LANG_PHOTO_COMMENT));
    }
//echo "&nbsp;[&nbsp;{PHOTO_COMMENT}&nbsp;]&nbsp;<a href='#' onClick=window.open('comment.php?Enr={PHOTO_COMMENT_ID}','','scrollbars=yes,width=300,height=500,left=50,top=50')><img src='comment.gif' align='absmiddle' border=0 alt='{PHOTO_COMMENT_TEXT}'></a>";
}
else{ // $comment_actif==TRUE
$temp->set_block_not("PHOTO", "PHOTO_COMMENT_B");
}*/
  $temp->parse("photo_affi", "PHOTO" ,true);
}

   // Acces direct aux pages

$temp->set_var(array("PHOTO_DEBUT" => 'Debut',
                     "PHOTO_DEBUT_DEBUT" => "?debut=0".$url2));
$nbr=$lignes / $h_aff;  // Nb de pages nécessaires
$fin=$lignes - ($lignes % $h_aff); // rang première image dernière page
$a=0;
$z=0;
$temp->set_block('photo', 'PHOTO_PAGES' ,'photo_pages');
$temp->set_var('photo_pages' , '');
while($a<$nbr){$a++;
      $temp->set_var(array('PHOTO_DEBUT_VAL_PAGE' => '?debut='.$z.$url2,
     'PHOTO_PAGE' =>$a));
   $temp->parse('photo_pages', 'PHOTO_PAGES',true);
$z+=$h_aff;}
$temp->set_var(array('PHOTO_FIN' => 'Fin',
               'PHOTO_DEBUT_VAL_FIN' => '?debut='.$fin.$url2));
// Pages precedentes & suivantes

if($debut>=$h_aff)
	{
	$pre=$debut-$h_aff;
     $temp->set_block('photo', 'PHOTO_PRECEDENT_LIEN' );
     $temp->set_var(array('PHOTO_DEBUT_PRECEDENT' => '?debut='.$pre.$url2,
     'PHOTO_PRECEDENT_TEXT' => 'Précedent'));
     $temp->set_block_not('photo', 'PHOTO_PRECEDENT' );
	}
else	{
	$pre=0;
 $temp->set_block('photo', 'PHOTO_PRECEDENT' );
     $temp->set_var(array('PHOTO_PRECEDENT' => 'Précedent'));
     $temp->set_block_not('photo', 'PHOTO_PRECEDENT_LIEN' );
	}
if ($debut+$h_aff<=$fin)
	{
	$sui=$debut+$h_aff;
 	$temp->set_block('photo', 'PHOTO_SUIVANT_LIEN' );
     $temp->set_var(array('PHOTO_DEBUT_SUIVANT' => '?debut='.$sui.$url2,
     'PHOTO_SUIVANT_TEXT' => 'Suivant'));
     $temp->set_block_not('photo', 'PHOTO_SUIVANT' );
	}
else
	{
	$sui=$fin;
     $temp->set_block('photo', 'PHOTO_SUIVANT' );
     $temp->set_var(array('PHOTO_SUIVANT' => 'Suivant'));
     $temp->set_block_not('photo', 'PHOTO_SUIVANT_LIEN' );
	}
 $temp->parse('CONTENU', 'photo');
 //}end else vide
?>
