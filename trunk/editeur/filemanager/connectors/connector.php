<?php 
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: connector.php
 * 	This is the File Manager Connector for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */
define("PAGE_ROOT","../../../");
include(PAGE_ROOT . 'extention.inc');
include(PAGE_ROOT . "config/config.php");
include(PAGE_ROOT . "include/constantes.php");
include(PAGE_ROOT . "include/fonction.php");
if (file_exists(PAGE_ROOT . "include/db/db_" . SQL_TYPE . ".php"))
  include_once(PAGE_ROOT . "include/db/db_" . SQL_TYPE . ".php");
else
  exit('<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( 'La Base de Donnée n\'existe pas' ) . '" />');
include(PAGE_ROOT . "include/config.php");
$sql = new DBTools(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);
$sql->connect();
if ($sql->Error()!= '')
   exit('<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sql->Error() ) . '" />');
$conf = new config('admin');
//include('config.php') ;
include('util.php') ;
include('io.php') ;
include('basexml.php') ;

//if ( !isset($Config['Enabled']) && !$Config['Enabled'])
	//SendError( 1, 'This connector is disabled. Please check the "editor/filemanager/browser/default/connectors/php/config.php" file' ) ;
if ( !isset( $_GET['Command'] ) )
		return ;

	// Get the main request informaiton.
	$sCommand		= $_GET['Command'] ;
	$sResourceType	= isset($_GET['Type'])?$_GET['Type']:0 ;
	$sCurrentFolder	= isset($_GET['CurrentFolder'])?$_GET['CurrentFolder']:0 ;

	// Check if it is an allowed type.
	if ( $sCommand == 'FileUpload' )
	{
		FileUpload( $sResourceType, $sCurrentFolder ) ;
		return ;
	}
    if ( $sCommand == 'GetFolders' )
	{
		include ('./GetFolders.php');
		return ;
	}
	CreateXmlHeader( $sCommand, $sResourceType, $sCurrentFolder ) ;
    if ( $sCommand == 'GetFoldersAndFiles' )
	{
		include ('./GetFoldersAndFiles.php');
	}
	if ( $sCommand == 'DeleteFile' )
	{
	   echo '<Error number="302" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
	}
	
	// Execute the required command.

	CreateXmlFooter() ;
?>