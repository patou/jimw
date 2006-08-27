<?php
  $menu_name = 'Album Photo';
  $sous_menu = array(array('photo'.EXT,'Photo'));
  function aff_menu_photo($tableau, $parent = 0, $prof = 0)
  {
    $sous_menu = array();
	   if (isset($tableau[$parent]))
	   {
		    for($deca='',$i=0;$i<=$prof;$i++)
		      $deca .= '&nbsp;>&nbsp;';
		    while(list($id, $titre) = each($tableau[$parent]))
		    {
		    		array_push($sous_menu, array('photo'.EXT.'?album='.$id, $deca . $titre));
					$sous_menu = array_merge($sous_menu, aff_menu_photo($tableau, $id, $prof+1));
		    }
	   }
	   
    return $sous_menu;
  }
  $query_cat = $sql->doQuery('SELECT id, parent, titre FROM cat_photo');
  $h = array();
  $i = 0;
  while ($Enr=$sql->fetch($query_cat))
	{
	   $h[$Enr->parent][$Enr->id] = $Enr->titre; 
	   $i++;
	}
  $sous_menu = array_merge($sous_menu, aff_menu_photo($h));
?>
