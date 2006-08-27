<?php
/**
 * Site du jeu risk Star Wars
 *
 * @version $Id: module_admin.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
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
$root = "../images";
$def_rac = "WE_JT_18-19_decembre_2004";
define("L_MINIATURE", 84);//largeur max des miniatures
define ("HMAXI_MINIATURE", 84);

//
//------------------------ Fonction --------------------------------------------
//
//make_photo_info(nom du repertoire,nom de la catÚgorie,tableau d'info des images);
function make_photo_info($dir,$cat_dir,$img){
global $racine;
     $file = "<"."?\n/////////////////////////////////////////////////\n//     Patrice De saint Steban                 //\n//        repertoire ".$dir."                  //\n/////////////////////////////////////////////////\n\t\$cat_dir = \"".$cat_dir."\";\n//\n// tableau des infos sur les images\n//\n";
     if (!empty($img)) {
             foreach($img as $nom =>$tabimg){
                     $file .="\n\t\$img[\"".$nom."\"] = array(\"".$tabimg[0]."\",\"".$tabimg[1]."\");";
                     }

             }
     $file .="\n?".">";
     $fp = fopen ($racine."/".$dir."/photo_info.php", "w+");
     fwrite ($fp, $file);
     fclose ($fp);
}

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
//make_thumb(nom du repertoire, nom du fichier);
function make_thumb($dir,$file){
global $racine;
if (!is_dir($racine."/".$dir."/mini")) mkdir($racine."/".$dir."/mini");
   $size = @getimagesize($racine."/".$dir."/".$file);
    $l=$size[0];
    $h=$size[1];
    $largeur=min($l,L_MINIATURE);
    $hauteur= floor($largeur * $h / $l);
    if ($hauteur > HMAXI_MINIATURE ){
        $largeur = floor($largeur * HMAXI_MINIATURE /$hauteur);
        $hauteur=HMAXI_MINIATURE;
    }
    $im = @imagecreatefromjpeg($racine."/".$dir."/".$file);
    $new_im = @imagecreatetruecolor($largeur,$hauteur);
    @imagecopyresized($new_im, $im, 0, 0, 0, 0,$largeur,$hauteur, $l,$h);
    @imagejpeg($new_im, $racine."/".$dir."/mini/".$file, 90);
}
//
//------------------------ code ------------------------------------------------
//

error_reporting(55);
	echo '<form action="photo.php" method="GET">
				<img src="./image/repertoire.gif"   border="0" width="17" height="17"><select name="rac">';
			if (isset($_REQUEST['rac'])) $rac = $_REQUEST['rac'];
			else $rac = $def_rac;
			$fp = opendir($root);
			while ($dir = readdir($fp))
			{
				 if ($dir!="." && $dir!="..")
				 {
					 if (is_dir($root."/".$dir)){
						 $cat_dir="";
						 $img="";
						 if (file_exists($root."/".$dir."/photo_info.php")) {
							 include($root."/".$dir."/photo_info.php");
							 echo '<option value="'.$dir.'" '.(($dir==$rac)?'selected':'').'>'.$cat_dir.'</option>';
						 }
						 else
							echo '<option value="'.$dir.'" '.(($dir==$rac)?'selected':'').'>'.$dir.'</option>';
					 }
				 }
			}
			  echo '</select>
			  	<input type="submit" value="Aller ..."></form>';

			$racine = $root . '/' . $rac;


switch($_GET["ac"]){
  case "aimg":
	        $dir=$_GET["dir"];
    	    $file =$_GET["file"];
        	include($racine."/".$dir."/photo_info.php");
			if (!empty($img[$file])){
						$title= $img[$file][0];
						$description=" (".$img[$file][1].")";
				   }
				   else {
						$exts = explode(".",$file);
						//$ext = ".".$exts[count($exts)-1];
						$title=$exts[0];
						$description="";
						}
			make_thumb($dir,$file);
			echo "<center><big>Image $file</big></center><hr color='red'><a href='$racine/$dir/$file'>
			<img src='$racine/$dir/mini/$file' border='0'></a> Titre : $titre<br>
			  Description : $description<br>
			  <a href='photo.php?rac=$rac&ac=eimg&dir=$dir&file=$file'>
			  <img src='./image/editer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;|&nbsp;&nbsp;
			  <a href='photo.php?rac=$rac&ac=dimg&dir=$dir&file=$file'><img src='./image/deplacer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='photo.php?rac=$rac&ac=simg&dir=$dir&file=$file'><img src='./image/supprimer.gif'  border='0' width='17' height='17' ></a>
			  <br><hr color='red'><a href='photo.php?rac=$rac&ac=ldir&dir=$dir'>Retour</a>";
			break;
	case "dimg":
			$ldir=$_GET["dir"];
			$file =$_GET["file"];
			if ($_GET["ok"]==1) {
				$ldir=$_GET["ldir"];
				$ndir=$_GET["ndir"];
				include($racine."/".$ldir."/photo_info.php");
				$titre=$img[$file][0];
				$description=$img[$file][1];
				include($racine."/".$ndir."/photo_info.php");
				$img[$file] = array($titre, $description);
				make_photo_info($ndir,$cat_dir,$img);
				@copy($racine."/".$ldir."/".$file,$racine."/".$ndir."/".$file);
				@unlink($racine."/".$ldir."/mini/".$file);
				if (file_exists($racine."/".$ldir."/mini/".$file)) {
					@copy($racine."/".$ldir."/mini/".$file,$racine."/".$ndir."/mini/".$file);
	     		}
					 $reussite="Fichier dÚplacÚ avec succÞs";
				   echo "<font color='red'>DÚplacement de $file rÚussis</font><br><hr color='red'>
				   <a href='photo.php?rac=$rac&ac=ldir&dir=$ndir'>retour Ó '$ndir'</a>&nbsp;&nbsp;
				   <a href='photo.php?rac=$rac&ac=ldir&dir=$ldir'>retour Ó '$ldir'</a>&nbsp;&nbsp;
				   <a href='photo.php?rac=$rac'>retour</a>";
					}
					else{
						$fp = opendir($racine);
						$select="";
						while ($dir = readdir($fp))
						{

						 if ($dir!="." && $dir!="..")
						 {
						 if (is_dir($racine."/".$dir)){
						 $cat_dir="";
						 $img="";

						 if (file_exists($racine."/".$dir."/photo_info.php")) {
						 include($racine."/".$dir."/photo_info.php");
						 $select.= "<option value='$dir' ";
						 if ($ldir==$dir) $select .="selected";
						 $select.=">$cat_dir</option>";
						 }
						 else {
						 $select.=  "<option value='$dir' ";
						 if ($ldir==$dir) $select .="selected";
						 $select.=">$dir</option>";
						 }
						 } }}
						 closedir($fp);
							 echo "<center><big>DÚplacement de l'image $file</big></center><hr color='red'>Vers la catÚgorie : <br>
							 <form action='photo.php' methode='GET'><input type='hidden' name='ac' value='dimg'>
							 <input type='hidden' name='ok' value='1'>
							 <input type='hidden' name='ldir' value='$ldir'>
							 <input type='hidden' name='rac' value='$rac'>
							 <input type='hidden' name='file' value='$file'>
							 <img src='./image/repertoire.gif'   border='0' width='17' height='17'><select name='ndir'>$select</select>
							 <input type='submit' value='DÚplacer'></form>";
			 }
			break;
  case "eimg":
			$dir=$_GET["dir"];
			$file =$_GET["file"];
			include($racine."/".$dir."/photo_info.php");
			if ($img[$file]!=""){
					$titre = $img[$file][0];
					$description = $img[$file][1];
					 }
					 else {
					 $exts = explode(".",$file);
					 $titre=$exts[0];
					 $description="";
					 }
					 make_thumb($dir,$file);
			echo "<center><big>Modifier l'image $file</big></center><br><a href='$racine/$dir/$file'>
			<img src='$racine/$dir/mini/$file' border='0'></a>
			<form action='photo.php' methode='GET'><input type='hidden' name='ac' value='ldir'>
			<input type='hidden' name='rac' value='$rac'>
			<input type='hidden' name='mimg' value='1'><input type='hidden' name='dir' value='$dir'>
			<input type='hidden' name='file' value='$file'> Titre : <input type='text' name='titre' value='$titre'><br>
			  Description : <input type='text' name='description' value='$description'><br>
			  <input type='submit' value='Modifier'></form><br><hr color='red'><a href='photo.php?rac=$rac'>Retour</a>";
			break;
  case "simg":
			$dir=$_GET["dir"];
			$file =$_GET["file"];
			if ($_GET["ok"]==1) {
				   @chmod($racine."/".$dir."/".$file,0777);
				   @unlink($racine."/".$dir."/".$file);
				   if (file_exists($racine."/".$dir."/mini/".$file)) {
					  @chmod($racine."/".$dir."/mini/".$file,0777);
					  @unlink($racine."/".$dir."/mini/".$file);
				  }
				   echo "<font color='red'>Supression de $file rÚussis</font><br>";
					}
					else{
							 echo "<font color='red' size='14'>Attention vous allez suprimer l'image $file <br>
							  Vous etes sur de vouloir le faire ?</font><br>
							  <a href='photo.php?rac=$rac&ac=simg&file=$file&dir=$dir&ok=1'>Oui, je suis sur !</a>&nbsp;&nbsp;|&nbsp;&nbsp;
							 <a href='photo.php?rac=$rac&ac=ldir&dir=$dir'>Non, c'&eacute;tait une erreur !</a><br>
							 <a href='photo.php?rac=$rac'>retour</a>";
			break;
			}
  case "ldir":
			$dir = $_GET["dir"];
			$file = $_GET["file"];
			echo "Liste des images de $dir</br>";
			if (!file_exists($racine."/".$dir."/photo_info.php")) make_photo_info($dir,$dir,"");
			include($racine."/".$dir."/photo_info.php");
			if ($_GET["mimg"]==1){ //modification des infos de l'image
				  $img[$file] = array($_GET["titre"], $_GET["description"]);
				  make_photo_info($dir,$cat_dir,$img);
				  }
			$fd = opendir($racine."/".$dir."/");
			while ($file = readdir($fd))
			{
			if (eregi(".jpeg",$file) OR eregi(".jpg",$file)){
				$thumb = $file;
				//make_thumb($dir,$file);
				if (file_exists($racine."/".$dir."/mini/".$file)) $thumb = "mini/".$file;
				   if (!empty($img[$file])){
						$title = $img[$file][0];
						$description = " (".$img[$file][1].")";
				   }
				   else {
						$exts = explode(".",$file);
						//$ext = ".".$exts[count($exts)-1];
						$title = $exts[0];
						$description = "";
						}
				echo "<a href='photo.php?rac=$rac&ac=aimg&dir=$dir&file=$file' class='infobulle'>
				<img src='./image/jpg.gif'   border='0' width='17' height='17'>$title
        <span><img src='$racine/$dir/mini/$file' border='0'></span>
        </a>$description&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href='photo.php?rac=$rac&ac=eimg&dir=$dir&file=$file'>
				<img src='./image/editer.gif'  border='0' width='17' height='17' ></a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href='photo.php?rac=$rac&ac=dimg&dir=$dir&file=$file'><img src='./image/deplacer.gif'  border='0' width='17' height='17' ></a>
				&nbsp;&nbsp;|&nbsp;&nbsp;<a href='photo.php?rac=$rac&ac=simg&dir=$dir&file=$file'>
				<img src='./image/supprimer.gif'  border='0' width='17' height='17' ></a><br>";
				}
		}
		closedir($fd);
		echo "<hr><a href='photo.php?rac=$rac'>retour</a>";
		break;
	case "adir":
			if (!is_dir($racine."/new")) mkdir($racine."/new");
			make_photo_info("new","nouvelle rubrique","");
			$dir="new";
	case "edir":
			if ($_GET["mdir"]==1){
					//modification du dir
					$dir=$_GET["dir"];
					$ncat_dir=$_GET["cat_dir"];
					$odir=$_GET["odir"];
					if ($odir!=$dir AND rep_valide($dir)==true) rename($racine."/".$odir,$racine."/".$dir);
					include($racine."/".$dir."/photo_info.php");
					make_photo_info($dir,$ncat_dir,$img);
					echo "<font color='red'>Modification reussis de $dir rÚussis</font><br>";
					}
			   else {
			if (empty($dir)) $dir=$_GET["dir"];
			if (is_dir($racine."/".$dir)){
			   if (file_exists($racine."/".$dir."/photo_info.php"))
			   include($racine."/".$dir."/photo_info.php");
			  else $cat_dir=$dir;
			  echo "<form action='photo.php' methode='GET'>
			  <input type='hidden' name='ac' value='edir'><input type='hidden' name='rac' value='$rac'>
			  <input type='hidden' name='mdir' value='1'>
			  Nom du repertoire : <input type='hidden' name='odir' value='$dir'><input type='text' name='dir' value='$dir'><br>
			  Nom de la rubrique : <input type='text' name='cat_dir' value='$cat_dir'><br>
			  <input type='submit' value='Modifier'></form><br><hr color='red'><a href='photo.php?rac=$rac'>Retour</a>";
			   } else die("le repertoire n'existe pas");
			break;    }
	default:
			$fp = opendir($racine);
			while ($dir = readdir($fp))
			{
				if ($dir!="." && $dir!="..")
			 	{
  			 	if (is_dir($racine."/".$dir)){
  			 		$cat_dir="";
  			 		$img="";
  			 		if (file_exists($racine."/".$dir."/photo_info.php")) {
  			 			include($racine."/".$dir."/photo_info.php");
  			 			echo "<a href='photo.php?rac=$rac&ac=ldir&dir=$dir'><img src='./image/repertoire.gif'   border='0' width='17' height='17'>
  						 &nbsp;&nbsp;$cat_dir</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='photo.php?rac=$rac&ac=edir&dir=$dir'>
  			  			 <img src='./image/editer.gif'  border='0' width='17' height='17' ></a><br>";
  			 		}
  			 	  else {
  			 		  echo "<a href='photo.php?rac=$rac&ac=ldir&dir=$dir'><img src='./image/repertoire.gif'   border='0' width='17' height='17'>&nbsp;&nbsp;$dir</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='photo.php?rac=$rac&ac=edir&dir=$dir'><img src='./image/editer.gif'  border='0' width='17' height='17' ></a><br>";
  			 	  }
			 } }}
			  echo "<hr color='red'><br><a href='photo.php?rac=$rac&ac=adir'>Cr&eacute;er une nouvelle cath&eacute;gorie</a>";
}

?>
