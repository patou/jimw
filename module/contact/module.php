<?php
/**
* Site du jeu Risk Star Wars
* 	module index
*
* @version $Id: module.php,v 1.1 2006/05/22 10:50:24 lesgrumels Exp $
* @copyright 2005
*/
$temp->set_file("contact" , "contact.htm"); //definie une page pour le site !
if (isset($_POST['submit'])){
  $temp->set_block("contact", "ENVOYER");
  $temp->set_var("MESSAGE_ENVOYER" , 'Votre message a été envoyé !');
  $temp->parse("CONTENU", "ENVOYER");
  mail($$conf->get->mail, $_POST['objet'], 'Un message sur le site de :' . $_POST['mail'] . ' à '. date("d/m/y h:m:s") ." \n". $_POST['text'], "From:".$_POST['mail'] );
//envoye du mail
}
else{
  $temp->set_block_not("contact", "ENVOYER");
  //$temp->set_var("contact_envoyer" , '');
  $temp->set_var("ADMIN_NOM_PAGE" , 'contact.php');
  $temp->parse("CONTENU", "contact");
}
?>
