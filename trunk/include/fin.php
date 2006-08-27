<?php

/**
 * Site internet du jeu Risk Star Wars
 *
 * @version $Id: fin.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
 * @copyright 2005
 **/
$sql->disconnect ();
$temp->set_var("TITRE", $conf->get("titrecamp"));
$temp->set_var("TITRE_PAGE", (isset($titre))?$titre:"");
$temp->set_var("FOOT", '&copy 2005-2006 - <a href="http://www.lesgrumels.fr">lesgrumels</a>');
$temp->pparse("resultat","page");
exit;
?>
