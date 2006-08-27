<?php
include("include/dbtools.class.php");
include("include/$conf->get.php");
$sql = DBTools::getInstance ();
$sql->connect();
$$conf->get = new $conf->get();
echo $sql->Error();
$$conf->get->modifier("login","mej303","admin");
$$conf->get->modifier("password","f79cdef77ee0fea3d0bd8c17b01d8f67","admin");
echo $sql->Error();
echo $sql->Error();
echo $$conf->get->login, "<br/>", $$conf->get->password, "<br/>";
?>
