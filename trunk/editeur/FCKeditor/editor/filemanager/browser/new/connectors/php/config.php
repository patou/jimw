<?php
/*
PHP Connector for the FCKEditor v2 File Manager
Written By Grant French, UK, Sept 2004
http://www.mcpuk.net

FCKEditor - By Frederico Caldeira Knabben
http://www.fckeditor.net

File Description:
Configuration file

ChangeLog:
09/09/2004 - Grant - 	First edition of file
20/12/2004 - Grant - 	Restructured some$fckphp_config variables, added authShareKey & 
			authTimeout re wrote all comments to try and make it 
			a bit neater.
		
*/


/*------------------------------------------------------------------------------*/
/* HTTP over SSL Detection (shouldnt require changing				*/
/*------------------------------------------------------------------------------*/
$fckphp_config['prot']="http://";
//$fckphp_config['prot'].=((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on')?"s":"");
//$fckphp_config['prot'].="://";
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* The physical path to the document root					*/
/*------------------------------------------------------------------------------*/
$fckphp_config['basedir']=$_SERVER['DOCUMENT_ROOT'];
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Prefix added to image path before sending back to editor			*/
/*------------------------------------------------------------------------------*/
$fckphp_config['urlprefix']=$fckphp_config['prot'].$_SERVER['SERVER_NAME'];
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Path to user files relative to the document root (no trailing slash)		*/
/*------------------------------------------------------------------------------*/
$fckphp_config['UserFilesPath']="file/";
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Progressbar handler (script that monitors upload progress) (''=none)
/*------------------------------------------------------------------------------*/
$fckphp_config['uploadProgressHandler']=''; //No upload progress handler
//$fckphp_config['uploadProgressHandler']=$fckphp_config['prot'].$_SERVER['SERVER_NAME']."/cgi-bin/progress.cgi"; //Perl upload progress handler
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Authentication (auth) :-								*/
/*  - Req		:: Boolean, whether authentication is required		*/
/*  - HandlerClass	:: Name of class to handle authentication in connector	*/
/*------------------------------------------------------------------------------*/
$fckphp_config['auth']['Req']= true;
$fckphp_config['auth']['HandlerClass']='Default';
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Settings for authentication handler :-					*/
/*  - SharedKey :: Shared encryption key (as set in test.php in example)	*/
/*------------------------------------------------------------------------------*/
//$fckphp_config['auth']['Handler']['SharedKey']="->Shared_K3y-F0R*5enD1NG^auth3nt1caT10n'Info/To\FILE,Brow5er--!";
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Per resource area settings:-							*/
/* - AllowedExtensions	:: Array, allowed file extensions (in lowercase)	*/
/* - AllowedMIME	:: Array, allowed mime types (in lowercase)		*/
/* - MaxSize		:: Number, Maximum size of file uploads in KBytes	*/
/* - DiskQuota		:: Number, Maximum size allowed for the resource area	*/
/* - HideFolders	:: Array, RegExp, matching folder names will be hidden	*/
/* - HideFiles		:: Array, RegExp, matching file names will be hidden	*/
/* - AllowImageEditing	:: Boolean, whether images in this area may be edited	*/
/*------------------------------------------------------------------------------*/

//File Area
$fckphp_config['ResourceAreas']['File'] =array(
	'AllowedExtensions'	=>	array("zip","doc","xls","pdf","rtf","csv","jpg","gif","jpeg","png","avi","mpg","mpeg","swf","fla"),
	'AllowedMIME'		=>	array(),
	'MaxSize'		=>	2048,
	'DiskQuota'		=>	-1,
	'HideFolders'		=>	array("^\."),
	'HideFiles'		=>	array("^\."),
	'AllowImageEditing'	=>	false
	);

//Image area
$fckphp_config['ResourceAreas']['Image'] =array(
	'AllowedExtensions'	=>	array("jpg","gif","jpeg","png"),
	'AllowedMIME'		=>	array(),
	'MaxSize'		=>	1024,
	'DiskQuota'		=>	-1,
	'HideFolders'		=>	array("^\."),
	'HideFiles'		=>	array("^\."),
	'AllowImageEditing'	=>	true
	);

//Flash area
$fckphp_config['ResourceAreas']['Flash'] =array(
	'AllowedExtensions'	=>	array("swf","fla"),
	'AllowedMIME'		=>	array(),
	'MaxSize'		=>	1024,
	'DiskQuota'		=>	-1,
	'HideFolders'		=>	array("^\."),
	'HideFiles'		=>	array("^\."),
	'AllowImageEditing'	=>	false
	);
	
//Media area
$fckphp_config['ResourceAreas']['Media'] =array(
	'AllowedExtensions'	=>	array("swf","fla","jpg","gif","jpeg","png","avi","mpg","mpeg"),
	'AllowedMIME'		=>	array(),
	'MaxSize'		=>	5120,
	'DiskQuota'		=>	-1,
	'HideFolders'		=>	array("^\."),
	'HideFiles'		=>	array("^\."),
	'AllowImageEditing'	=>	false
	);
	
/*==============================================================================*/		


/*------------------------------------------------------------------------------*/
/* Global Disk Quota - Max size of all resource areas				*/
/*------------------------------------------------------------------------------*/
$fckphp_config['DiskQuota']['Global']=-1; //In MBytes (default: 50mb)
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Directory and File Naming :-							*/
/*  -MaxDirNameLength	:: Maximum allowed length of a directory name		*/
/*  -DirNameAllowedChars :: Array of characters allowed in a directory name	*/
/*  -FileNameAllowedChars :: Array of characters allowed in a file name		*/
/*------------------------------------------------------------------------------*/

$fckphp_config['MaxDirNameLength']=25;

$fckphp_config['DirNameAllowedChars']=array();

	//Allow numbers
	for($i=48;$i<58;$i++) array_push($fckphp_config['DirNameAllowedChars'],chr($i));
	
	//Allow lowercase letters
	for($i=97;$i<123;$i++) array_push($fckphp_config['DirNameAllowedChars'],chr($i));
	
	//Allow uppercase letters
	for($i=65;$i<91;$i++) array_push($fckphp_config['DirNameAllowedChars'],chr($i));
	
	//Allow space,dash,underscore,dot
	array_push($fckphp_config['DirNameAllowedChars']," ","-","_",".");
	
$fckphp_config['FileNameAllowedChars']=$fckphp_config['DirNameAllowedChars'];
array_push($fckphp_config['FileNameAllowedChars'],')','(','[',']','~');
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Debugging :-									*/
/*  - Debug	:: Boolean, if set to true a copy of the connector output is 	*/
/*			sent to a file as well as to the client.		*/
/*  - DebugOutput :: File to send debug output to (absolute path)		*/
/*------------------------------------------------------------------------------*/
$fckphp_config['Debug']=false;
$fckphp_config['DebugOutput']="/var/www/fckeditor/htdocs/data/fck_conn_dbg";
/*==============================================================================*/


/*------------------------------------------------------------------------------*/
/* Internals :-									*/
/*	ResourceTypes :: Array of valid resource areas				*/
/*	Commands :: Array of valid commands accepted by the connector		*/
/*------------------------------------------------------------------------------*/
$fckphp_config['ResourceTypes'] = array('File','Image','Flash','Media');
$fckphp_config['Commands'] = array(
				"CreateFolder",
				"GetFolders",
				"GetFoldersAndFiles",
				"FileUpload",
				"Thumbnail",
				"DeleteFile",
				"DeleteFolder",
				"GetUploadProgress",
				"RenameFile",
				"RenameFolder"
				);
/*==============================================================================*/
?>