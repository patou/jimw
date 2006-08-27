<?php

/**
* Site internet du jeu Risk-Star Wars fon
*
* @version $Id: fonction.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
// toutes les fonction général du site
// connexion à la base
function protege_texte($texte)
{
  $tab_search = array('\'', '\"');
  $tab_replace = array('&#39', '&quot;');
  return  addslashes(str_replace($tab_search, $tab_replace ,$texte));
}

function deprotege_texte($texte)
{
  $tab_search = array('&#39', '&quot;');
  $tab_replace = array('\'', '\"');
  return  stripslashes(str_replace($tab_search, $tab_replace ,$texte));
}


?>
