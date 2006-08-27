<?php

/**
* Site du jeu Risk Star Wars
* 	installation
* 
* @version $Id: install.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/

include_once('./include/dbtools.class.php');
$sql = DBTools::getInstance ();
$xml = simplexml_load_file('sql.xml');
echo 'Installation du site ... Merci de patienter';
//if (isset ($xml->script)){
  try {
       $sql->doQuery ('BEGIN');
       foreach ($xml->script->query as $query){
	   	  echo $query, '<br />';
          $sql->doQuery ($query);
       }
       $sql->doQuery ('COMMIT');
    }catch (Exception $e){
       $sql->doQuery ('ROLLBACK');
       $sql->disconnect ();
       throw new Exception ('Impossible de créer la base de données ['.$e->getMessage ().']');
}
echo "Installation terminé<br/>";
//}
?>