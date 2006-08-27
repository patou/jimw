<?php

/**
* Site du jeu Risk Star Wars
* 	module download
* 
* @version $Id: module.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
function unite($octet){
    $unite_spec = array("octets","Ko","Mo","Go","To");
    $count=0;
    while($octet>=1024){
        $count++;
        $octet/=1024;
    }
    return(sprintf('% 0.2f',$octet) . " " . $unite_spec[$count]);
}
$temp->set_file("download" , "download.htm"); //definie une page pour le site !
$temp->set_var("PRESENTATION" , "Téléchargements des rapports, executables et sources");
//affichage des rubriques !
$racine = PAGE_ROOT . REP_DOWNLOAD;
$fp = opendir($racine);
$temp->set_block("download", "REPERTOIRE", "repertoire");
$temp->set_var("repertoire", "");
$temp->set_block("download", "FICHIERS");
//$temp->set_var("fichiers", "");
while($dir = readdir($fp)){
	$racine_dir = PAGE_ROOT . REP_DOWNLOAD . $dir . '/';
	if ($dir <> '.' AND $dir <> '..' AND is_dir($racine_dir)) { //c'est un repertoire
		$temp->set_var("REP_TITRE", $dir );
		$temp->set_var("REP_FICHIERS", '' );
		$fpd = opendir($racine_dir);
		while($file = readdir($fpd)){
			if ($file <> '.' AND $file <> '..' AND !ereg("(.htm|.html|.php|.js|.css)$", $file)) { //evite d'afficher ces deux fichiers
			    //$nom = explode(".",$page); 
				$temp->set_var(array("FILE_LIEN" => $racine_dir . $file, "FILE_NOM" => $file, "FILE_SIZE" => unite(filesize($racine_dir . $file))));
				$temp->parse("REP_FICHIERS", "FICHIERS", true);
			}
		}
		closedir($fpd);
		//$temp->set_var("REP_FICHIERS", $temp)
		
		$temp->parse("repertoire", "REPERTOIRE", true);
	}
} 
closedir($fp);
$temp->set_var("FICHIERS", '' );
$temp->parse("CONTENU", "download");

?>