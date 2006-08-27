<?php
$query = "SELECT * FROM menu";
$menu_sql = $sql->doQuery($query);
function aff_cat_rec($tableau, $parent = 0, $prof = 1)
{
  global $temp;
  if (isset($tableau[$parent]))
  {
    $str = '';
    $temp->set_var("SOUS_MENU", "");
    while(list($id, $tab) = each($tableau[$parent]))
    {
        	$temp->set_var(array("SUBMENU_LIEN" => $tab[2],
                               "SUBMENU_TITRE" => deprotege_texte($tab[0]),
                               "SUBMENU_DESCRIPTION" => deprotege_texte($tab[1]),
                               "SUBMENU_TARGET" => ($tab[3]=='')?'_SELF':$tab[3],
                               "SUBMENU_SOUS" => aff_cat_rec($tableau, $id, $prof+1)));
         	$str .= $temp->subst("SUB_MENU");

    }
    return $str;
  }
  else
    return '';
}

$temp->set_block("page", "MENU", "menu2");
//$temp->set_var("menu", "");
$temp->set_block("page", "SUB_MENU");
//$temp->set_block("page", "MENU2", "menu2");
//$temp->set_var("menu2", "");

/* affiche un menu déroulant avec tous le menu hiérarchique */
    $sql_par = $sql->doQuery('SELECT * FROM menu ORDER BY place');
    $h = array();
		$i = 0;
		$parent = 0;
    while ($Enr=$sql->fetch($sql_par))
		{
			   $h[$Enr->parent][$Enr->id] = array($Enr->titre, $Enr->description, $Enr->url, $Enr->target);
			   if (strpos($Enr->url, $_SERVER['SCRIPT_NAME']) > 0)
			     $parent = $Enr->parent;
			   $i++;
		}
		$prof = 1;
		while(list($id, $tab) = each($h[$parent]))
    {
        	$temp->set_var(array("MENU_LIEN" => $tab[2],
                               "MENU_TITRE" => deprotege_texte($tab[0]),
                               "MENU_DESCRIPTION" => deprotege_texte($tab[1]),
                               "MENU_TARGET" => ($tab[3]=='')?'_SELF':$tab[3],
                               "SOUS_MENU" => aff_cat_rec($h, $id, 2)));
        $temp->parse("menu2", "MENU", true);
	/*	$temp->set_var(array("MENU_LIEN" => $tab[2],
                               "MENU_TITRE" => deprotege_texte($tab[0]),
                               "MENU_DESCRIPTION" => deprotege_texte($tab[1]),
                               "MENU_TARGET" => ($tab[3]=='')?'_SELF':$tab[3],
                               "SOUS_MENU" => aff_cat_rec($h, $id, 2)));
        $temp->parse("menu2", "MENU2", true);*/
    }
$temp->set_var("SUB_MENU", '');
?>
