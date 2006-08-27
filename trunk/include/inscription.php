<?php 
/*
################################################################################
####   fichiers inscription.php du site des camps mej                       ####
####    créer par Patrice et Emmanuel de Saint Steban                       ####
####    lesgrumels@wanadoo.fr                                               ####
####    tout droits réservé                                                 ####
################################################################################
*/
require("connexion.php");
function aide ($aide) {
    global $host;
 	return "&nbsp;&nbsp;<script><!--\n".
	'document.write("<a href=\"javascript:window.open(\''.$host.'aide/index.php?aide_statut=site&aide='.
	$aide.
	"', 'aide_Hautefeuille.org', 'scrollbars=yes,resizable=yes,width=740,height=580'); ".
	'void(0);\">");'.
	"\n// --></script><noscript>".
	'<a href="'.$host.'aide/index.php?aide_statut=site&aide='.
	$aide.
	'" target="_blank"></noscript><img src="'.$host.'aide/aide.gif" alt="AIDE" title="De l\'aide sur cet &eacute;l&eacute;ment" width="12" height="12" border="0" align="middle"></a>';
}
if ($action=="ajout"){
if (empty($login))$erreur.="Le  login n'a pas été remplis<br>";
if (empty($pass)) $erreur.="Le  mots de passe n'a pas été remplis<br>";
if (empty($pass2)) $erreur.="Vous n'avez pas retaper le mot de passe<br>";
if (empty($nom)) $erreur.="Le nom n'a pas été saisis<br>";
if (empty($prenom)) $erreur.="Le prenom n'a pas été saisis<br>";
if (empty($adresse)) $erreur.="L'adresse n'a pas été remplis<br>";
if (empty($postal)) $erreur.="Le code postale n'a pas été remplis<br>";
if (empty($ville)) $erreur.="La ville n'a pas été remplis<br>";
if (empty($mail)) $erreur.="L'adresse e-mail n'a pas été remplis<br>";
if (empty($tel))  $erreur.="Le numero de telephone n'a pas été rentré<br>";
if ($pass!=$pass2){
 $erreur.="les deux mots de passes sont différents<br>";
}
if (!ereg("([A-Za-z0-9._-]+)([@]{1})(([A-Za-z0-9]+)((\.)+)([A-Za-z]{2,}))",$mail)){
    $erreur.="l'e-mail est invalide<br>";
}
if (!ereg("^[a-zA-Z][a-zA-Z0-9]{3,13}$",$login)){
    $erreur.="le login doit commencer par une lettre, et avoir minimum 4 caractéres<br>";
}
if (!ereg("^[0-9]{5}$",$postal)){
    $erreur.="le cote Postal n'est pas valide<br>";
}
if (!ereg("^[0-9]{4}$",$year)){
    $erreur.="l'année n'est pas exact<br>";
}
list($user,$domaine) = split("@",$mail,2);
/*if (!checkdnsrr($domaine,"MX")){
$erreur.="le domaine $domaine ne peut resevoire d'email";
} */
$requet="SELECT incr FROM membre WHERE username LIKE '$login'";
$num_query=mysql_query($requet);
$num=mysql_num_rows($num_query);
if(mysql_errno()!=0)
        die ("<BR>INDEX. ".mysql_errno().": ".mysql_error()."<verification login;");
if ($num!=0){
 $erreur.="le login est déjà utilisé, choisisez s'en un autre<bR>";
}
$requet_mail="SELECT email FROM membre WHERE email LIKE '$mail'";
$req_mail=mysql_query($requet);
$num_mail=mysql_num_rows($num_query);
if(mysql_errno()!=0)
        die ("<BR>INDEX. ".mysql_errno().": ".mysql_error()."<verification du mail;");
if ($num_mail!=0){
 $erreur.="l'email est déjà utilisé, choisisez s'en un autre<br>";
}
if (empty($erreur) AND isset($action) AND $action=="ajout"){

  require ("haut.php");
 // ON RECHERCHE L'ID MAXIMUM DE LA TABLE
	$requete_idmax=mysql_query("select max(incr) from membre") or die(mysql_error()."<recherche de l'id");
 $requete_idmax_forum=mysql_query("select max(user_id) from forum_users") or die(mysql_error());
	$idmax=mysql_result($requete_idmax,0,"max(incr)");
 $idmax+=1;
 $idmax_forum=mysql_result($requete_idmax_forum,0,"max(user_id)");
 $idmax_forum+=1;
$PWD_Hash = md5(stripslashes($pass));
$login=addslashes($login);
$prenom=addslashes($prenom);
$nom=addslashes($nom);
$mail=addslashes($mail);
$adresse=addslashes($adresse);
$ville=addslashes($ville);
$signature=addslashes($signature);
$passion=addslashes($passion);
$activite=addslashes($activite);
$presentation=addslashes($presentation);
$autre=addslashes($autre);
$website=addslashes($website);
$requet_forum="INSERT INTO `forum_users` (`user_id`, `user_active`, `username`, `user_password`, `user_session_time`, `user_session_page`, `user_lastvisit`, `user_regdate`, `user_level`, `user_posts`, `user_timezone`, `user_style`, `user_lang`, `user_dateformat`, `user_new_privmsg`, `user_unread_privmsg`, `user_last_privmsg`, `user_emailtime`, `user_viewemail`, `user_attachsig`, `user_allowhtml`, `user_allowbbcode`, `user_allowsmile`, `user_allowavatar`, `user_allow_pm`, `user_allow_viewonline`, `user_notify`, `user_notify_pm`, `user_popup_pm`, `user_rank`, `user_avatar`, `user_avatar_type`, `user_email`, `user_icq`, `user_website`, `user_from`, `user_sig`, `user_sig_bbcode_uid`, `user_aim`, `user_yim`, `user_msnm`, `user_occ`, `user_interests`, `user_actkey`, `user_newpasswd`)
VALUES ('$idmax_forum', '1', '$login', '$PWD_Hash', '0', '0', '0', '0', '0', '0', '2.00', '1', 'french', 'D M d, Y g:i a', '0', '0', '0', '', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '', '0', '$mail', '$ICQ', '$website', '$ville', '$signature', '', '$aim', '$yim', '$msn', '', '$passion', '', '')";
$requet_insert="INSERT INTO `membre` (`incr`, `username`, `password`, `firstname`, `lastname`, `website`, `email`, `showemail`, `perms`, `rooms`, `gender`, `adresse`, `ville`, `postal`,  `naissance`, `tel`, `portel`, `activite`, `passion`,`presentation`,`autre`,`ICQ`,`forum`)
 VALUES ('$idmax', '$login', '$PWD_Hash', '$prenom', '$nom', '$website','$mail','1', 'user', '$rooms', '$gender', '$adresse', '$ville', '$postal', '$year-$mois-$jour', '$tel', '$portel', '$activite', '$passion', '$presentation', '$autre', '$ICQ','$idmax_forum')";
	if ($result_insert=mysql_query($requet_insert)) echo"insertion reussis<br>";
 else echo "erreur dans l'insertion ", mysql_errno(), ":" , mysql_error(),"<br>";
 if ($result_forum=mysql_query($requet_forum)) echo"insertion dans le forum reussis<br>";
 else echo "erreur dans l'insertion dans le forum", mysql_errno(), ":" , mysql_error(),"<br>";
  print("<br><hr><b> MERCI </b>de vous étes inscrit sur Hautefeuille.org nous esperons que nos services vous servirons à communiquer avec vos membres de la famille.  Pour toute sugestion, merci de nous en faire par sur <a href='mailto:info@hautefeuille.org'>info@hautefeuille.org</a>");
 $carrehaut="<form action=\"identification.php\" methode=\"get\"><table width=\"140\" valign=\"top\" align=\"right\" ><tr>
<td colspan='2'><h2>Identification</h2></td></tr>
<tr><td>Login :</td><td><small><input name=\"login\" type=\"text\"  size=\"8\"></small></td></tr>
<tr><td>Mot de passe : </td><td><small><input name=\"pass\" type=\"password\" size=\"8\" class=\"login\" ></small></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td><input value=\"envoyer\" type=\"submit\" class=\"login\" ></td><td>
<input value=\"effacer\" type=\"reset\" ></td></tr></table></form>";
$carrebas="";
require ("bas.php");
}}
if ($action=="inscription" OR isset($erreur)){
?>
<html><head><title>Inscription au site hautefeuille.org</title></head><script language="JavaScript" type="text/javascript">
<!-- function emoticon(text) {
	text = ' ' + text + ' ';
	if (document.post.message.createTextRange && document.post.message.caretPos) {
		var caretPos = document.post.message.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		document.post.message.focus();
	} else {
	document.post.message.value  += text;
	document.post.message.focus();
	}
}//-->
</script>

<body onload='javascript:form.login.focus();'>
<form method='post' action='inscription.php' name='form' >
       <input type='hidden' name='action' value='ajout'>
  <table width='60%' valign='top' align='center' border='1' bordercolor='#339933' cellspacing='0' height='129'>
    <tr>
      <td colspan='2' bgcolor='#339933'>
        <div align='center'><H1>Inscription au site Hautefeuille.org </h1><br></div>
      </td>
    </tr>
    <tr>
      <td  colspan='2'><font color='red' >&nbsp;les Champs suivit d'un * sont obligatoire</font></td>

    </tr>
    <?php if (isset($erreur)) echo"<tr><td width='' colspan=2>&nbsp;<h1 color='red'>erreur:</H1><font color='red'>&nbsp;$erreur</font></td></tr>";
    else echo"<tr><td width='' colspan=2>&nbsp;</td></tr>";
    ?>
   <tr >
      <td width='30%' bgcolor='#ffffcc'>Login </td>
      <td width='200'>
        <input type='text' name='login' maxlength='14'/ value='<?=$login?>' ><font color='red' >*<?=aide("login")?></font> moins de 14 lettres
      </td>
    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>Mots de passe</td>
      <td width='200'>
        <input type='password' name='pass' maxlength='10'/ ><font color='red' >*</font>(maximum 10 lettres)
      </td>
    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>Retaper le mot de passe</td>
      <td width='200'>
        <input type='password' name='pass2' maxlength='10'/><font color='red' >*</font>
      </td>
    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>Nom</td>
      <td width='200'>
        <input type='text' name='nom' maxlength='30'/ value='<?php =$nom?>'><font color='red' >*</font>
      </td>
    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>Pr&eacute;nom</td>
      <td width='200'>
        <input type='text' name='prenom' maxlength='30'/ value='<?php =$prenom?>'><font color='red' >*</font>
      </td>
    </tr>
      <tr>
      <td width='30%' bgcolor='#ffffcc'>Telephone</td>
      <td width='200'>
        <input type='text' name='tel' size='10' maxlength='10'/ value='<?php =$tel?>'><font color='red' >*</font>
      </td>
    </tr>
	<tr >
      <td width='30%' bgcolor='#ffffcc'>Telephone portable</td>
      <td width='200'>
        <input type='text' name='portel' size='10' maxlength='10'/ value='<?php =$portel?>'>
      </td>
    </tr>
	<tr >
      <td width='30%' bgcolor='#ffffcc'>e-mail</td>
      <td width='200'>
        <input type='text' name='mail' maxlength='30'/ value='<?php =$mail?>'><font color='red' >*</font>
      </td>
    </tr>
<tr >
      <td width='30%' bgcolor='#ffffcc'>Age</td>
      <td width='200'>
        <input type='text' name='age' size='2' maxlength='2'/ value='<?php =$age?>'><font color='red' >*</font>
      </td>
    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>sexe</td>
      <td width='200'>
        <INPUT TYPE="radio" NAME="gender" VALUE="1" <?if ($gender==1) echo "CHECKED";?> >&nbsp;masculin<BR>
				<INPUT TYPE="radio" NAME="gender" VALUE="2" <?if ($gender==2) echo "CHECKED";?> >&nbsp;féminin
      </td>
    </tr>
      <tr >
      <td width='30%' bgcolor='#ffffcc'>Adresse</td>
      <td width='200'>
        <input type='text' name='adresse' maxlength='40'/ value='<?php =$adresse?>'><font color='red' >*</font>
      </td>

    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>code postal / ville</td>
      <td width='400'>
         <input type='text' name='postal' size='5' maxlength='5'/ value='<?php =$postal?>'>&nbsp;&nbsp;<input type='text' name='ville' maxlength='20'/ value='<?php =$ville?>'><font color='red' >*</font>
      </td>
      <tr>
        <td valign='top'width='30%' bgcolor='#ffffcc'><font face=$font_face size=$font_size>Date de naissance : </font></td>
        <td width='400'><select name='jour' style=' font-family : verdana, arial, helvetica, sans serif;' size='1'>
	<option value='00' selected></option>
        <?php 
        for($a=1;$a<32;$a++) {
            if($a<10) $b="0$a";
            else $b="$a";
            echo"<option value='$b'" ;
        if($jour==$b) echo "selected";
        echo">$b</option>";}
        ?>
      </select><select name='mois' size='1'>
  <?php 
  $m=array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
  for($a=0;$a<13;$a++){
      if ($a<10)$b="0$a";
      else $b="$a";
      echo"<option value='$b' ";
      if ($mois==$b) echo "selected";
      echo ">$m[$a]</option>";
   }
   ?>
      </select><font face='Verdana, Helvetica, Arial' size='1'><input TYPE='TEXT' NAME='year' SIZE='4' MAXLENGTH='4' value='19'></font><font color='red' >*</font></p>
        </td>
      </tr>
      <tr >
      <td width='30%' bgcolor='#ffffcc'>Site Internet</td>
      <td width='200'>
        <input type='text' name='website' maxlength='40'/ value='<?php =$website?>'>
      </td></tr>
      <tr>
      <td width='30%' bgcolor='#ffffcc'>messagerie instantanne (ICQ)</td>
      <td width='400'>
        icq: <input type='text' name='icq' size='10' maxlength='15'/ value='<?php =$icq?>'><br>adresse AIM:<input type='text' name='aim' maxlength='255'/ value='<?php =$ville?>'><br>
      </td>

    </tr>
      <tr >
      <td width='30%' bgcolor='#ffffcc' >Les activitées que tu pratiques</td>
      <td width='200'>
        <textarea name='activite' onfocus='javascript:if(this.value==this.defaultValue && this.value!="<?php =$activite?>") this.value="";'><?if(isset($activite))echo $activite; else echo "Tes activités que tu pratique"; ?></textarea>
      </td>
      <tr >
      <td width='30%' bgcolor='#ffffcc'>Tes passion</td>
      <td width='200'>
        <textarea name='passion' onfocus='javascript:if(this.value==this.defaultValue) this.value="";'><?if(isset($passion))echo $passion; else echo "Echanger sur vos passion ";?></textarea>
      </td>

    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>un petit texte de présentation</td>
      <td width='200'>
        <textarea name='presentation' onfocus='javascript:if(this.value==this.defaultValue) this.value="";'><?if(isset($presentation))echo $presentation; else echo "ta description personnel";?></textarea>
      </td>

    </tr>
    <tr >
      <td width='30%' bgcolor='#ffffcc'>Autre</td>
      <td width='200'>
        <textarea name='autre' onfocus='javascript:if(this.value==this.defaultValue) this.value="";'><?if(isset($autre))echo $autre; else echo "Si tu veux ajouter autre chose";?></textarea>
      </td>
      <tr >
      <td width='30%' bgcolor='#ffffcc'>Signature:<br />Ceci est un bloc de texte qui peut être ajouté aux messages que vous postez. Il y a une limite de 255 caractères<br /><br />Le HTML est <u>activé</u><br />Le <a href="../../forum/faq.php?mode=bbcode&amp;sid=3c7fa07a8299d2b3af5158d09069d833" target="_phpbbcode">BBCode</a> est <u>Activé</u><br /><a href='http://localhost/Hautefeuille.org/forum/posting.php?mode=smilies&sid=1c1f763a0f6195d1f1503280c4c632f5' target="_phpbbcode">Les Smilies sont </A><u>Activés</u></td>
	  <td width='200'>
		<textarea name="signature"style="width: 300px"  rows="6" cols="30"></textarea>
	  </td>

    </tr>



    <tr>
      <td height='125' width='30%'><input type='submit' value='inscription'></td>
      <td height='125' width='30%'><input type='reset' value='supprimer'></td>
    </tr>
  </table>
</form>
<?php 

?>
                </body></html>
<?php 
}
if (empty($action)){
 if (isset($login) AND isset($pass)){
    if (($login=="LOUP" AND $pass=="HARDI") OR ($login=="loup" AND $pass=="hardi")){
?>
<H1>Inscription au site Hautefeuille.org</H1>
<p>Les administrateurs et modérateurs de ce site s'efforceront de supprimer ou éditer tous les messages à caractère répréhensible aussi rapidement que possible. Toutefois, il leur est impossible de passer en revue tous les messages. Vous admettez donc que tous les messages postés sur ces forums expriment la vue et opinion de leurs auteurs respectifs, et non pas des administrateurs, ou modérateurs, ou webmestres (excepté les messages postés par eux-même) et par conséquent ne peuvent pas être tenus pour responsables.<br><br>

Vous consentez à ne pas poster de messages injurieux, obscènes, vulgaires, diffamatoires, menaçants, sexuels ou tout autre message qui violerait les lois applicables. Le faire peut vous conduire à être banni immédiatement de façon permanente (et votre fournisseur d'accès à internet en sera informé). L'adresse IP de chaque message est enregistrée afin d'aider à faire respecter ces conditions. Vous êtes d'accord sur le fait que le webmestre, l'administrateur et les modérateurs de ce forum ont le droit de supprimer, éditer, déplacer ou verrouiller n'importe quel sujet de discussion à tout moment. En tant qu'utilisateur, vous êtes d'accord sur le fait que toutes les informations que vous donnerez ci-après seront stockées dans une base de données. Cependant, ces informations ne seront divulguées à aucune tierce personne ou société sans votre accord. Le webmestre, l'administrateur, et les modérateurs ne peuvent pas être tenus pour responsables
si une tentative de piratage informatique conduit à l'accès de ces données. <br><br>

Ce forum utilise les cookies pour stocker des informations sur votre ordinateur. Ces cookies ne contiendront aucune information que vous aurez entré ci-après, ils servent uniquement à améliorer le confort d'utilisation. L'adresse email est uniquement utilisée afin de confirmer les détails de votre enregistrement ainsi que votre mot de passe (et aussi pour vous envoyer un nouveau mot de passe dans la cas où vous l'oublieriez).   <br> <br>

En vous enregistrant, vous vous portez garant du fait d'être en accord avec le règlement ci-dessus.  <bR><br>

</p>
<form action="inscription.php" methode="GET"><input type="hidden" name="action" value="inscription"><input type="hidden" name="site" value="<?php =$site?>"><input type="hidden" name="nomsite" value="<?php =$nomsite?>"><input type="hidden" name="forum_groupe" value="<?php =$forum_groupe?>">
<input type="submit" value="j'accepte"><input type="button" value="je refuse" onclick="javascript:history.back(1)"></form>
 <?php 
}
else{ $e=1;}
}
if ((empty($login) OR  empty($pass)) OR isset($e)){
?>
<html><head><title>Identification</title></head><body onload='javascript:document.form.login.focus();'><DIV align=center><H1><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Site Hautefeuille.org </b></H1></DIV>
<?php 
if (isset($e)){
?>
<H1><font color='#FF0000' >L'identifiant et/ou le mots de passe sont incorect !</font></H1>
<?php 
}
else {
?>
<H3><font color='#FF0000' >Pour pouvoir s'inscrire sur Hautefeuille.org, Il faut rentrer un login et mot de passe, Si vous ne l'avez pas demander le à l'adresse suivantes :<br><ul><a href="mailto:info@Hautefeuille.org?subject=mot de passe pour l'inscription sur Hautefeuille.org">info@Hautefeuille.org</a></ul></font></H3>
<?php 
}
?>

<form action='<?php =$PHP_SELF?>' methode='get'name='form'><table width='300' valign='top' align='center' border='1' bordercolor='#339933'><tr>
<td colspan='2' bgcolor='#339933'><h2 ><font color='#FFFFFF' >Veuillez vous identifier </font></h2></td></tr>
<tr bgcolor='#99cc00'><td bgcolor='#ffffcc' >Login :</td><td><small><input name='login' type='text'  ></small></td></tr>
<tr bgcolor='#99cc00' ><td bgcolor='#ffffcc' >Mot de passe : </td><td><small><input name='pass' type='password'  class='login' ></small></td></tr>
<tr><td colspan='2'>j'ai perdu mon mots de passe</td></tr>
<tr><td align='center' colspan='2'><input value='envoyer' type='submit' class='login' >
</td></tr></table></form>
<hr width="80%">
<DIV align=center><FONT face="Verdana, Arial, Helvetica, sans-serif"
      size=2><a href='inscription.php'>s'inscrire</a><br>Site Hautefeuille.org © 2002<br><a href='mailto:info@Hautefeuille.org'>nous écrire</a></FONT></DIV></TD>
    <TD vAlign=top align=left width=142 height=25>&nbsp;</TD></TR></TBODY></TABLE>

<DIV align=center></DIV>
<P>&nbsp;</P></body></html>
<?php 
}}
?>
