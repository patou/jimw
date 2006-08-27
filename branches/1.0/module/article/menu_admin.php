<?php
  $menu_name = 'Pages';
  $sous_menu[] = array('add=1','Ajouter une page');
  function aff_menu($tableau, $parent = 0, $prof = 0)
  {
    $sous_menu = array();
	   if (isset($tableau[$parent]))
	   {
		    for($deca='',$i=1;$i<=$prof;$i++)
		      $deca .= '&nbsp;>&nbsp;';
		    while(list($id, $titre) = each($tableau[$parent]))
		    {
		    		array_push($sous_menu, array('article'.EXT.'?nom='.$id, $deca . $titre));
					$sous_menu = array_merge($sous_menu, aff_menu($tableau, $id, $prof+1));
		    }
	   }
	   
    return $sous_menu;
  }
  $query_cat = $sql->doQuery("SELECT id, parent, titre FROM article WHERE rubrique LIKE '1'");
  $h = array();
  $i = 0;
  while ($Enr=$sql->fetch($query_cat))
	{
	   $h[$Enr->parent][$Enr->id] = $Enr->titre; 
	   $i++;
	}
  $sous_menu = array_merge($sous_menu, aff_menu($h));
?>
