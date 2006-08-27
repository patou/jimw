<?php
	echo '<Folders>' ;
	$query_cat = $sql->doQuery('SELECT id, titre FROM cat_photo WHERE parent LIKE "'.$sCurrentFolder.'"');
    while ($Enr=$sql->fetch($query_cat))
	{
	   echo '<Folder name="' , ConvertToXmlAttribute( $Enr->titre ) , '" id="' , ConvertToXmlAttribute( $Enr->id ) , '" />' ; 
	}
	echo '</Folders>' ;

	// Send the files
	//natcasesort( $aFiles ) ;
	echo '<Files>' ;
	$query_file = $sql->doQuery('SELECT id, titre FROM photos WHERE rubrique LIKE "'.$sCurrentFolder.'"');
    while ($Enr=$sql->fetch($query_file))
	{
	   echo '<File name="' , ConvertToXmlAttribute( $Enr->titre ) , '" id="' ,URL_ROOT . REP_PHOTO, ConvertToXmlAttribute( $Enr->id ) , '.jpg', '" editable="1" 
	   thumb="' ,URL_ROOT . REP_PHOTO.'mini/', ConvertToXmlAttribute( $Enr->id ) , '.jpg', '" size="', @filesize(PAGE_ROOT . REP_PHOTO.'mini/'.  $Enr->id  . '.jpg'),'"/>' ; 
	}
	/*foreach ( $aFiles as $sFiles )
		echo $sFiles ;
*/
	echo '</Files>' ;
?>