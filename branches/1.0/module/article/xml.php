<?php 
header('Content-type:text/xml');
echo '<?xml version="1.0"?'.'>';
$racine = '../../article';
$fp = opendir($racine);
echo '<tree id="0">';
while($page = readdir($fp)){
	if (ereg("^([a-zA-Z0-9_]*).htm$", $page)) {
		$nom = explode(".",$page);
		echo '<item id="', $nom[0], '" text="', $nom[0], '"></item>';
	}
} // while
echo "</tree>";
?>