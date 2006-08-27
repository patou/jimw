<?php
$config['nom_site'] = array('Nom du Site', 'ligne', 'Mon site Web', array('size' => 50));
$config['description'] = array('Description', 'texte', 'Descriptiont de votre site', array());
$dir_theme = opendir(PAGE_ROOT . REP_TEMPLATE);
while ($temp_theme = readdir($dir_theme)) {
   if (is_dir(PAGE_ROOT . REP_TEMPLATE . $temp_theme) AND $temp_theme[0] <> '.') {
        $theme[$temp_theme] = $temp_theme;
      }
}
closedir($dir_theme);
$config['template'] = array('Style de la Page', 'liste', 'default', array('liste' => $theme));
//$config['password'] = array('Mots de Passe', 'pass', 'pass', array());
$config['couleur'] = array('Couleur', 'couleur', '#000000', array());
$config['mail'] = array('E-Mail', 'mail', 'mail@mail.com', array());
?>