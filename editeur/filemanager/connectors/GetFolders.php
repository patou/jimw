<?php
function aff_xml($tableau, $select = 0, $parent = 0, $prof = 0)
{
  if (isset($tableau[$parent]))
  {
    while(list($id, $titre) = each($tableau[$parent]))
    {
    		echo '<item id="', $id, '" ', (($id == $select)?'select="yes"':''),' text="', ConvertToXmlAttribute(deprotege_texte($titre)), '" open="1">';
			aff_xml($tableau, $select, $id, $prof+1);
			echo '</item>';
    }

  }
}

function creer_tab_arbo($req)
{
 global $sql;
 $query_cat = $sql->doQuery($req);
  $h = array();
  $i = 0;
  while ($Enr=$sql->fetch($query_cat))
	{
	   $h[$Enr->parent][$Enr->id] = $Enr->titre; 
	   $i++;
	}
	return $h;
}
	SetXmlHeaders() ;
	$racine = PAGE_ROOT . REP_ARTICLE;
	$fp = opendir($racine);
	echo '<tree id="0">';
	aff_xml(creer_tab_arbo("SELECT id, parent, titre FROM cat_photo"));
	echo "</tree>";
	exit();
?>