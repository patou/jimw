<?php 
session_start();
if (isset($_GET['logout'])){
	session_destroy();
	setcookie ('session_nom');
	header("location:admin.php");
	exit;
}
if (isset($_POST['auth'])) { //creation de la session
	if ($_POST['login']== $conf->get("login") and MD5($_POST['pass'])== $conf->get("password")) {
		//$req = mysql_query("SELECT id,nom FROM user WHERE login LIKE '".$_POST['login']."' AND pass LIKE '".md5($_POST['pass'])."'", $sql);
		//$res = mysql_fetch_array($req);
		//if (isset($res['id'])){
		setcookie ( 'session_nom'  , $conf->get("login")  , time()+60*60);
		//}
		//else die("Access Interdit !");
	}
	else die("Access Interdit !");
}
else {
if (!isset($_COOKIE['session_nom']) || empty($_COOKIE['session_nom'])) { //Pas authentifier
?>
<div align="center"><h3>Authentification</h3>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="auth" value="1">
<table width='300' valign='top' align='center' border='1' bordercolor='#339933'><tr>
<td colspan='2' bgcolor='#339933'><h2 ><font color='#FFFFFF' >Veuillez vous identifier </font></h2></td></tr>
<tr bgcolor='#99cc00'><td bgcolor='#ffffcc' >Login :</td><td><small><input name='login' type='text'  ></small></td></tr>
<tr bgcolor='#99cc00' ><td bgcolor='#ffffcc' >Mot de passe : </td><td><small><input name='pass' type='password'  class='login' ></small></td></tr>
<tr><td colspan='2'><a href="mailto:webmasteur@hautefeuille.org">j'ai perdu mon mot de passe</a></td></tr>
<tr><td align='center' colspan='2'><input value='envoyer' type='submit' class='login' >
</td></tr></table></form>
<hr />
<a href="index.php">Retour</a>
</div>
</body>
</html>
<?php 
exit;
}

}

?>
