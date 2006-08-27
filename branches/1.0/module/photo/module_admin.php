<?php
/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin.php,v 1.1 2006/05/22 12:03:40 lesgrumels Exp $
 * @copyright 2005
 **/
echo '  <style type="text/css">
  .infobulle span {
  display: none;
}
.infobulle:hover {
  text-decoration: none;
  background: #FFFFFF;
}
.infobulle:hover span {
  display: block;
  position: absolute;
  left: 3em;
  margin-top: 1em;
  border: 2px solid #00008d;
  font-weight: normal;
  }
</style>';
//
//---------------------------- Conf --------------------------------------------
//
$root = PAGE_ROOT . REP_PHOTO;
$def_rac = "";
define("L_MINIATURE", 200);//largeur max des miniatures
define ("HMAXI_MINIATURE", 320);

//
//------------------------ Fonction --------------------------------------------
//

function rep_valide($rep){
if (str_replace("\\",".",$rep)!=$rep){return false;}
if (str_replace("/",".",$rep)!=$rep){return false;}
if (str_replace(":",".",$rep)!=$rep){return false;}
if (str_replace("*",".",$rep)!=$rep){return false;}
if (str_replace("?",".",$rep)!=$rep){return false;}
if (str_replace("\"",".",$rep)!=$rep){return false;}
if (str_replace("<",".",$rep)!=$rep){return false;}
if (str_replace(">",".",$rep)!=$rep){return false;}
if (str_replace("|",".",$rep)!=$rep){return false;}
return true;
}

function parse_titre($titre, $categorie, $id, $nom)
{
  $titre = str_replace('%c',$categorie,$titre);
  $titre = str_replace('%i',$id,$titre);
  $nnom = explode('.', $titre);
  $titre = str_replace('%n', $nnom[0], $titre);
  return $titre;
}
//make_thumb(nom du repertoire, nom du fichier);
function make_thumb($file){
global $root;
if (!is_dir($root."mini")) mkdir($root."mini");
    $size = getimagesize($root.$file);
    $l=$size[0];
    $h=$size[1];
    $largeur=min($l,L_MINIATURE);
    $hauteur= floor($largeur * $h / $l);
    if ($hauteur > HMAXI_MINIATURE ){
        $largeur = floor($largeur * HMAXI_MINIATURE /$hauteur);
        $hauteur=HMAXI_MINIATURE;
    }
    if (!$im = imagecreatefromjpeg($root.$file))
    {
      echo '<font color=red>Impossible de créer une image : ', $file, '<br />Erreur : imagecreatefromjpeg</font>';
      return;
    }
    if (!$new_im = imagecreatetruecolor($largeur,$hauteur) AND !$new_im = imagecreate($largeur,$hauteur)){
      echo '<font color=red>Impossible de créer une image imagecreate ', $file, '<br />Erreur : imagecreatetruecolor</font>';
      return;
    }
    if (!imagecopyresized($new_im, $im, 0, 0, 0, 0,$largeur,$hauteur, $l,$h))
    {
        echo '<font color=red>Impossible de redimentionner l\'image : ', $file, '<br />Erreur : imagecopyresized</font>';
      return;
    }
    if (!imagejpeg($new_im, $root."mini/".$file, 90))
    {
      echo '<font color=red>Impossible d\'enregistrer l\'image: ', $file, '<br />Erreur : imagejpeg</font>';
      return;
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
	   $h[$Enr->parent][$Enr->id] = array($Enr->titre, $Enr->nb);
	   $i++;
	}
	return $h;
}

function aff_select($tableau, $select = 0, $parent = 0, $prof = 0)
{
  if (isset($tableau[$parent]))
  {
    for($deca='',$i=0;$i<=$prof;$i++)
      $deca .= '&nbsp;>&nbsp;';
    while(list($id, $tab) = each($tableau[$parent]))
    {
    			echo '<option value="', $id, '" ', (($id == $select)?'selected':''), '>', $deca, ' ', $tab[0], '</option>';
			aff_select($tableau, $select, $id, $prof+1);
    }

  }
}

function aff_rep($tableau, $parent = 0, $prof = 0)
{
  if (isset($tableau[$parent]))
  {
    for($deca='',$i=0;$i<$prof;$i++)
      $deca .= '&nbsp;>&nbsp;';
    while(list($id, $tab) = each($tableau[$parent]))
    {
    		echo "<tr><td>$deca<a href='", ADMIN_NOM_PAGE, "ac=ldir&dir=$id'><img src='./images/repertoire.gif'   border='0' width='17' height='17'>$tab[0]</a></td><td>$tab[1] images</td>
               <td><a href='", ADMIN_NOM_PAGE, "ac=edir&dir=$id'><img src='./images/editer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;|&nbsp;
               <a href='", ADMIN_NOM_PAGE, "ac=sdir&sdir=1&dir=$id'><img src='./images/supprimer.gif'  border='0' width='17' height='17' ></a></td></tr>";
        aff_rep($tableau, $id, $prof+1);
    }

  }
}
//
//------------------------ code ------------------------------------------------
//
	if (isset($_REQUEST['rac'])) $rac = $_REQUEST['rac'];
			else $rac = $def_rac;
	if (isset($_REQUEST['ac'])) $action = $_REQUEST['ac'];
			else $action = '';
	if (isset($_REQUEST['dir'])){
   $dir = $_REQUEST['dir'];
	 $req = $sql->doQuery("SELECT titre, nb FROM cat_photo WHERE id LIKE '$dir'");
	 $tmp = $sql->fetch($req);
	 $dir_name = $tmp->titre;
	}
  else
  {
   $dir = '0';
   $dir_name = 'root';
  }
/*
error_reporting(55);

*/

switch($action){
  case 'uimg':
      echo '<h2>Envoyer des images :</h2><br />
        Vous avez trois méthodes pour envoyer des images :
        <ul><li>Envoyer vos photos (au format JPEG) en gr&acirc;ce &agrave; un logiciel FTP, dans le répertoire upload à la racine du site.
        Puis de vous rendre dans sur la <a href="', ADMIN_NOM_PAGE, '&ac=timg">page suivante</a></li>
        <li>Vous pouvez compressez vos photos dans un fichier zip (< 2mo) toutes vos photos et l\'envoyer ici :</br>
          <form method="POST" action="', ADMIN_NOM_PAGE, 'ac=zimg" enctype="multipart/form-data">
          <input type="hidden" name="ac" value="zimg">
          <input type="file" name="zip_file" size="25">
          <input type="submit" value="envoyer"></form></li>';

	    echo '<li>Envoyer vos images cinq par cinq gr&acirc;ce au fomulaire ci-dessous</a> au format jpeg.</li>
	         </ul>
          <form method="POST" action="', ADMIN_NOM_PAGE, '" enctype="multipart/form-data">
          <input type="hidden" name="aimg" value="1">
          <input type="hidden" name="ac" value="aimg">';
          if (isset($_GET['dir']))
            echo '<input type="hidden" name="dir" value="', $dir, '">';
          else
          {
            $tab = creer_tab_arbo("SELECT * FROM cat_photo");
            echo 'Rubrique : <select name="dir">';
				 aff_select($tab, $dir);      
			echo '</select><br />';
          }
          echo ' <input type="hidden" name="aimg" value="1">
          <input type="hidden" name="ac" value="aimg">
          <table border=0 cellspacing=5><tr><th>Image</th><th> Titre </th><th> Description</th></tr>';
	    for ($i = 1; $i <= 5; $i++)
	       echo '
            <tr><td><input type="hidden" name="img[]" value="', $i, '"><input type="file" name="img_file[', $i, ']" size="25"></td>
            <td><input type="text" size="40" maxlength="75" name="titre[', $i, ']"></td>
            <td><input type="text" size="65" name="description[', $i, ']"></td></tr>';
      echo '</table><br /><input type="submit" value="Envoyer"></form><hr color="red" width="500" align="left"><a href="', ADMIN_NOM_PAGE, 'ac=ldir&dir=$dir">Retour</a>';
      break;
  case 'zimg':
      $file = (isset($_FILES['zip_file'])) ? $_FILES['zip_file'] : '';
      if ($file!='' AND is_uploaded_file($file['tmp_name'])){ //si le fichier est bien envoyer
          //décompression du ficher
          echo 'Decompression du zip ... ', $file['name'];
          include(PAGE_ROOT . 'include/pclzip.lib.php');
          $zip = new pclzip($file['tmp_name']);
           if (($list = $zip->listContent()) == 0) {
  		    echo "Error : ".$zip->errorInfo(true);
  			 }
  
  			 for ($i=0; $i<sizeof($list); $i++) {
    		 for(reset($list[$i]); $key = key($list[$i]); next($list[$i])) {
      		 echo "File $i / [$key] = ".$list[$i][$key]."<br>";
    		 }
    		 echo "<br>";
  			 }
          if ($zip->extract(PCLZIP_OPT_PATH, PAGE_ROOT . 'upload/',
                        PCLZIP_OPT_REMOVE_ALL_PATH) == 0) {
    	    echo 'Error : '.$zip->errorInfo(true);
         }
      }
      else
      {
        echo '<font color="red">L\'envoye du fichier à échoué.';
      }
  case 'timg':
      $img = (isset($_POST['img']))?$_POST['img']:array();
      		echo '<form method="POST" action="', ADMIN_NOM_PAGE, '">
          <input type="hidden" name="dir" value="', $dir, '">
          <input type="hidden" name="ac" value="jimg">
          <input type="hidden" name="jimg" value="1">';
          if (isset($_GET['dir']))
            echo '<input type="hidden" name="dir" value="', $dir, '">';
          else
          {
           $tab = creer_tab_arbo("SELECT * FROM cat_photo");
            echo 'Rubrique : <select name="dir">';
				 aff_select($tab, $dir);      
			echo '</select><br />';
          }
          echo 'Voici les images que vous avez envoy&eacute; sur le serveur et qui ne sont pas
          encore ajout&eacute; dans l\'album. Pour envoyer vos photos, il suffit de les envoyer en ftp dans le répertoire upload/ gr&acirc;ce à un logiciel ftp.
          <table border=0 cellspacing=5><tr><th>&nbsp;
          <img src="./images/editer.gif"  border=0 width=17 height=17 ></th><th> Titre </th><th> Description</th></tr>';
          $fp = opendir('./upload/');
          $i = 1;
    		while ($file = readdir($fp))
			   {
			     if (preg_match('!\.jp[e]?g$!i', $file))
			     echo '
            <tr><td><input type="checkbox" name="img[]" value="', $i, '">
            <input type="hidden" name="file[', $i, ']" value="', $file, '">
            <a href="#" class="infobulle">
				    <img src="./images/jpg.gif"   border="0" width="17" height="17">', $file,
            '<span><img src="./upload/', $file, '" border="0" width="100"></span></a></td>
            <td><input type="text" size="50" maxlength="75" name="titre[', $i, ']" value="', $file, '"></td>
            <td><input type="text" size="75" name="description[', $i, ']" value=""></td></tr>';
           $i++;
		    }
		    closedir($fp);
    echo '</table>
    <input type="submit" value="Ajouter"></form><br><hr color="red" width="500" align="left"><a href="', ADMIN_NOM_PAGE, 'ac=ldir&dir=', $dir, '">Retour</a>';
    break;
  case 'eimg':
      $img = (isset($_POST['img']))?$_POST['img']:array();
      if (count($img) > 0)
      {
      		$dir = $_POST['dir'];
    	 	  $result = $sql->doQuery("SELECT * FROM photos WHERE rubrique LIKE '$dir'");
    	   	echo 'Modification de ', count($img), ' images</br>
          <form method="POST" action="', ADMIN_NOM_PAGE, '">
          <input type="hidden" name="dir" value="', $dir, '">
          <input type="hidden" name="ac" value="mimg">
          <input type="hidden" name="mimg" value="1">
          <table border=0 cellspacing=5><tr><th>&nbsp;
          <img src="./images/editer.gif"  border=0 width=17 height=17 ></th><th> Titre </th><th> Description</th></tr>';
    		while ($Enr = $sql->fetch($result))
			   {
			     if (in_array($Enr->id, $img))
           echo '
            <tr><td><input type="hidden" name="img[]" value="', $Enr->id, '">
            <a href="#" class="infobulle">
				    <img src="./images/jpg.gif"   border="0" width="17" height="17">', $Enr->id, '.jpg
            <span><img src="', $root, 'mini/', $Enr->id, '.jpg" border="0" width="100"></span></a></td>
            <td><input type="text" size="50" maxlength="75" name="titre[', $Enr->id, ']" value="', stripslashes(str_replace("&#39;", "'", $Enr->titre)), '"></td>
            <td><input type="text" size="75" name="description[', $Enr->id, ']" value="', stripslashes(str_replace('&#39;', '\'', $Enr->description)), '"></td></tr>';
		    }
    echo '</table>
    <input type="submit" value="Modifier"></form><br><hr color="red" width="500" align="left"><a href="', ADMIN_NOM_PAGE, 'ac=ldir&dir=', $dir, '">Retour</a>';
    	}
 			break;
  case 'dimg':
      $dir = $_POST['dir'];
			$img = (isset($_POST['img']))?$_POST['img']:array();
			if (count($img) > 0)
			{
			 $ddir = $_POST['ddir'];
       while (list(,$image) = each($img)){
          $result = $sql->doQuery("UPDATE photos SET rubrique = '$ddir' WHERE id LIKE '$image'");
       }
       $nb_req = $sql->doQuery("SELECT COUNT(id) AS nb FROM photos WHERE rubrique LIKE '$dir'");
	   $nb = $sql->fetch($nb_req);
	   $req = $sql->doQuery("UPDATE cat_photo SET nb = $nb->nb WHERE id LIKE '$dir'");
	   echo '<font color=green>Déplacement de '.count($img).' image réussis</font><br>';
       $dir = $ddir;
       $nb_req = $sql->doQuery("SELECT COUNT(id) AS nb FROM photos WHERE rubrique LIKE '$dir'");
	   $nb = $sql->fetch($nb_req);
	   $req = $sql->doQuery("UPDATE cat_photo SET nb = $nb->nb WHERE id LIKE '$dir'");
			}
			$simg = 0;
  case 'simg':
			$dir = $_POST['dir'];
			$img = (isset($_POST['img']))?$_POST['img']:array();
			if (!isset($simg) AND count($img) > 0)
			{
       while (list(,$image) = each($img)){
          $result = $sql->doQuery("DELETE FROM photos WHERE id LIKE '$image'");
          @chmod($root . $image . '.jpg', 0777);
				  @unlink($root . $image . '.jpg');
				  if (file_exists($root . 'mini/' . $image . '.jpg')) {
					  @chmod($root . 'mini/' . $image . '.jpg', 0777);
					  @unlink($root . 'mini/' . $image . '.jpg');
				  }
       }
       $nb_req = $sql->doQuery("SELECT COUNT(id) AS nb FROM photos WHERE rubrique LIKE '$dir'");
	   $nb = $sql->fetch($nb_req);
       $req = $sql->doQuery("UPDATE cat_photo SET nb = $nb->nb WHERE id LIKE '$dir'");
	   echo '<font color=green>Supression de '.count($img).' image réussis</font><br>';

			}
	case 'mimg':
	    $img = (isset($_POST['img'])) ? $_POST['img'] : array();
	    if (isset($_POST['mimg']) AND count($img) > 0){
        while (list(,$image) = each($img)){
          $titre = (isset($_POST['titre'][$image])) ? str_replace('\'', '&#39;', htmlentities($_POST['titre'][$image])) : '';
          $description = (isset($_POST['description'][$image])) ? str_replace('\'', '&#39;', htmlentities($_POST['description'][$image])) : '';
          $result = $sql->doQuery("UPDATE photos SET titre = '$titre', description = '$description' WHERE id LIKE '$image'");
        }
      echo '<font color=green>Modification réussis de ', count($img), ' images</font><br />';
      }
	case 'aimg':
	    $img = (isset($_POST['img'])) ? $_POST['img'] : array();
	    $file = (isset($_FILES['img_file'])) ? $_FILES['img_file'] : array();
	    $dir = (isset($_POST['dir']))?$_POST['dir']:0;
	    //print_r($file);
	    if (isset($_POST['aimg']) AND count($img) > 0){
	      $req = $sql->doQuery("SELECT Max(id) + 1 AS maxid FROM photos"); //nouveau ID
	      $tmp = $sql->fetch($req);
	      $id = $tmp->maxid;
	      $nb = 0;
        while (list(, $image) = each($img)){
          if (is_uploaded_file($file['tmp_name'][$image])){ //si le fichier est bien envoyer
            if( !move_uploaded_file($file['tmp_name'][$image], $root.$id.'.jpg') )
               echo '<font color=red>Impossible de déplacer le fichier</font>';
            else
            {
              make_thumb($id.'.jpg');
              $titre = (isset($_POST['titre'][$image]) && !empty($_POST['titre'][$image])) ? str_replace('\'', '&#39;', htmlentities(parse_titre($_POST['titre'][$image],$dir_name, $id, $file['name'][$image]))) : $file['name'][$image];
              $description = (isset($_POST['description'][$image])) ? str_replace('\'', '&#39;', htmlentities($_POST['description'][$image])) : '';
              $result = $sql->doQuery("INSERT INTO photos ( id , titre , rubrique , date , description )
                          VALUES ('$id', '$titre', '$dir', '".date("Y-m-d")."', '$description');");
              $id++;
              $nb++;
            }
          }
          else
            echo '<br /><font color=red>Fichier '.$image.' non envoyé</font>';
        }
      $nb_req = $sql->doQuery("SELECT COUNT(id) AS nb FROM photos WHERE rubrique LIKE '$dir'");
	  $nb = $sql->fetch($nb_req);
	  $req = $sql->doQuery("UPDATE cat_photo SET nb = $nb->nb WHERE id LIKE '$dir'");
	  echo '<br /><font color=green>Ajout réussis de ', count($nb), ' images</font><hr width=500 color=red />';
      }
  case 'jimg':
	    $img = (isset($_POST['img'])) ? $_POST['img'] : array();
	    $dir = (isset($_POST['dir']))?$_POST['dir']:0;
	    if (isset($_POST['jimg']) AND count($img) > 0){
	      $req = $sql->doQuery("SELECT Max(id) + 1 AS maxid FROM photos"); //nouveau ID
	      $tmp = $sql->fetch($req);
	      $id = $tmp->maxid;
	      $nb = 0;
        while (list(, $i) = each($img)){
            if (!isset($_POST['file'][$i]) AND !empty($_POST['file'][$i]))
              echo '<font color=red>Le ficher n\'existe pas </font><br />';
            else
            {
              $file = $_POST['file'][$i];
              echo 'Ajout de la photo : ', $file, '</br>';
              if(!copy('./upload/'.$file, $root.$id.'.jpg'))
                 echo '<font color=red>Impossible de déplacer le fichier ', $file, '</font><br />';
              else
              {
                if (!(@chmod('./upload/'.$file, 0777) AND @unlink('./upload/' . $file)))
                  echo '<font color=red>Impossible de supprimer le fichier ./upload/',$file,'</font><br />';
                make_thumb($id.'.jpg');
                $titre = (isset($_POST['titre'][$i]) && !empty($_POST['titre'][$i])) ? str_replace('\'', '&#39;', htmlentities(parse_titre($_POST['titre'][$i],$dir_name, $id, $file))) : $file;
                $description = (isset($_POST['description'][$i])) ? str_replace('\'', '&#39;', htmlentities($_POST['description'][$i])) : '';
                $result = $sql->doQuery("INSERT INTO photos ( id , titre , rubrique , date , description )
                          VALUES ('$id', '$titre', '$dir', '".date("Y-m-d")."', '$description');");
                $id++;
                $nb++;
              }
            }
            
        }
        $nb_req = $sql->doQuery("SELECT COUNT(id) AS nb FROM photos WHERE rubrique LIKE '$dir'");
		$nb = $sql->fetch($nb_req);
		$req = $sql->doQuery("UPDATE cat_photo SET nb = $nb->nb WHERE id LIKE '$dir'");
		echo '<br /><font color=green>Ajout réussis de ', count($nb), ' images</font><hr width=500 color=red />';
        }
  case 'ldir':
      if (!isset($dir))
			   $dir = $_GET['dir'];
			$file = (isset($_GET['file']))?$_GET['file']:'';
			$nb_req = $sql->doQuery("SELECT COUNT(id) AS nb FROM photos WHERE rubrique LIKE '$dir'");
			$nb = $sql->fetch($nb_req);
			$req = $sql->doQuery("UPDATE cat_photo SET nb = $nb->nb WHERE id LIKE '$dir'");
			$result = $sql->doQuery("SELECT * FROM photos WHERE rubrique LIKE '$dir'");

	if ($tmp->nb == 0)
	       echo '<script language="javascript">
         window.location = "', ADMIN_NOM_PAGE, '";
        </script>';
	echo 'Liste des images de ', $dir_name, '</br>
			<script language="javascript">
      <!--
      function supprimer(form)
      {
          if (confirm("Etes vous sur de supprimer ces images ?")){
              form.submit();
              return true;
          }
          return false;
      }
      -->
      </script>
      <form method="POST" action="', ADMIN_NOM_PAGE, '">
      <input type="hidden" name="dir" value="', $dir, '">';
			while ($Enr = $sql->fetch($result))
			{
			  if (file_exists($root.$Enr->id.'.jpg')){
          //if (!file_exists($root.'mini/'.$Enr->id.'.jpg'))
			      //make_thumb($Enr->id.'.jpg');
          echo '
            <input type="checkbox" name="img[]" value="',$Enr->id,'"><a href="', $root, $Enr->id, '.jpg" class="infobulle">
				    <img src="./images/jpg.gif"   border="0" width="17" height="17">', $Enr->titre,'
            <span><img src="', $root, 'mini/', $Enr->id, '.jpg" border="0" width="100"></span>
            </a> (', $Enr->date, ') ', $Enr->description, '<br />';
       		}
       		else
       			$result = $sql->doQuery("DELETE FROM photos WHERE id LIKE '$Enr->id'");
		}
		echo 'Pour la selection<input type="image" src="./images/editer.gif" name="ac" value="eimg" title="Modifier les images">
          <input onClick="return supprimer(this);" name="ac" type="image" src="./images/supprimer.gif" value = "simg">';
    	  $tab = creer_tab_arbo("SELECT * FROM cat_photo");
          echo 'Rubrique : <select name="ddir">';
				 aff_select($tab, $dir);      
		  echo '</select>';
						echo '<input type="image" src="./images/deplacer.gif" name="ac" value="dimg" title="D&eacuteplacer les images"></form>
            <hr color="red" width="50" align="left"><br><a href="', ADMIN_NOM_PAGE, '">Retour</a>&nbsp;<a href="', ADMIN_NOM_PAGE, 'ac=uimg&dir=', $dir, '">Envoyer des Images</a>';
		break;
	case 'adir':
		$req = $sql->doQuery("SELECT Max(id) + 1 AS maxid FROM cat_photo"); //nouveau ID
	    $tmp = $sql->fetch($req);
	    $dir = $tmp->maxid;
	    $req = $sql->doQuery("INSERT INTO cat_photo ( id , titre, parent , nb ) VALUES ( '$dir' , 'nouveau', '0' , '0' ) ");
	case 'edir':
			if (!isset($dir))
	       $dir = $_POST['dir'];
			if (isset($_POST['mdir']) AND !empty($_POST['mdir'])){
					$titre = (isset($_POST['titre'])) ? str_replace('\'', '&#39;', htmlentities($_POST['titre'])) : '';
					$req_num = $sql->doQuery('SELECT COUNT(id) AS num FROM photos WHERE rubrique LIKE '. $dir);
					$res_num = $sql->fetch($req_num);
					$num = $res_num->num;
					$parent = (isset($_POST['parent'])) ? $_POST['parent'] : 0;
					if (!$req = $sql->doQuery("UPDATE cat_photo SET titre = '$titre', parent = '$parent', nb = '$num' WHERE id LIKE '$dir'"))//, nb = (SELECT COUNT(id) FROM photos WHERE rubrique LIKE '$dir')
          echo "<font color='red'>Modification reussis de $titre réussis</font><br>";
			}
			else {
			 $req = $sql->doQuery("SELECT titre, parent FROM cat_photo WHERE id LIKE '$dir'");
	     $tmp = $sql->fetch($req);
	     echo '<form action="', ADMIN_NOM_PAGE, '" method="POST">
			         <input type="hidden" name="ac" value="edir">
			         <input type="hidden" name="mdir" value="1"><input type="hidden" name="dir" value="', $dir, '">
			         Nom de la rubrique : <input type="text" name="titre" value="', stripslashes(str_replace("&#39;", "'", $tmp->titre)), '"><br>
               Parent : <select name="parent"><option value="0" ', ($tmp->parent==0)?'selected':'','>Racine</option>';
			        $tab = creer_tab_arbo("SELECT * FROM cat_photo");
            		aff_select($tab , $tmp->parent);      
			echo '</select><input type="submit" value="Modifier"></form><br><hr color="red"><a href="', ADMIN_NOM_PAGE, '">Retour</a>';
			  break;
      }
  case 'sdir':
      if (isset($_GET['sdir']) AND !empty($_GET['sdir'])){
         $dir = $_GET['dir'];
         $req = $sql->doQuery("SELECT titre, nb FROM cat_photo WHERE id LIKE '$dir'");
         $tmp = $sql->fetch($req);
	     if (isset($tmp->titre))
	     {
	      	$req_num = $sql->doQuery('SELECT COUNT(id) AS num FROM cat_photo WHERE parent LIKE '. $dir);
		    $res_num = $sql->fetch($req_num);
			$num = isset($res_num->num)?$res_num->num:-1;
			if ($num == 0 && $tmp->nb == 0)
			{
	           $req = $sql->doQuery("DELETE FROM cat_photo WHERE id LIKE '$dir'");
               echo '<font color=green>Suppression réussis de ', $tmp->titre, '</font><br />';
			}
			else
				echo '<font color=red>La catégorie ', $tmp->titre, ' n\'est pas vide ! impossible de la supprimer</font><br />';
         }
      }
	default:
	    echo '<table><tr><th>Titre</th><th>Nb</th><th>Action</th></tr>';
			$h = creer_tab_arbo("SELECT * FROM cat_photo ORDER BY parent ASC");
			aff_rep($h, 0);

			  echo '</table><hr color="red" width="500" align="left"><br><a href="', ADMIN_NOM_PAGE, 'ac=adir">Cr&eacute;er une nouvelle cat&eacute;gorie</a>&nbsp;<a href="', ADMIN_NOM_PAGE, 'ac=uimg">Envoyer des Images</a>';
}

?>
