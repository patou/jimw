<?php
/*
PHP Connector for the FCKEditor v2 File Manager
Written By Grant French, UK, Sept 2004
http://www.mcpuk.net

FCKEditor - By Frederico Caldeira Knabben
http://www.fckeditor.net

File Description:
Main connector file, implements the State Pattern to 
redirect requests to the appropriate class based on 
the command name passed.
*/

error_reporting(E_ALL);
require_once "config.php";

if (!isset($_SERVER['DOCUMENT_ROOT'])) $_SERVER["DOCUMENT_ROOT"] = substr($_SERVER['PATH_TRANSLATED'], 0, strlen($_SERVER['SCRIPT_NAME']) * - 1);

if ($fckphp_config['Debug']===true) ob_start();
outputHeaders();

//These are the commands we may expect
$valid_commands=$fckphp_config['Commands'];
$valid_resource_types=$fckphp_config['ResourceTypes'];

//Get the passed data
$command=(
		((isset($_GET['Command']))&&($_GET['Command']!=""))?
			$_GET['Command']:
			""
		);
		
$type=(
		((isset($_GET['Type']))&&($_GET['Type']!=""))?
			$_GET['Type']:
			"File"
		);
		
$cwd=str_replace("..","",
		(
		((isset($_GET['CurrentFolder']))&&($_GET['CurrentFolder']!=""))?
			$_GET['CurrentFolder']:
			"/"
		)
		);
		
$cwd=str_replace("..","",$cwd);

$extra=(
		((isset($_GET['ExtraParams']))&&($_GET['ExtraParams']!=""))?
			$_GET['ExtraParams']:
			""
		);

if (in_array($command,$valid_commands)) {

	if ($fckphp_config['auth']['Req']) {
		require_once "./Auth/".$fckphp_config['auth']['HandlerClass'].".php";
		
		$auth=new Auth();
		$fckphp_config=$auth->authenticate($extra,$fckphp_config);
		if ($fckphp_config['authSuccess']!==true) {
			header ("content-type: text/xml");
			echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
			?><Connector command="authentication_failed" resourceType="authentication_failed">
				<CurrentFolder path="authentication_failed" url="authentication_failed" />
				<Error number="-1" />
			</Connector><?php
			exit(0);
		}
	}

	//bit of validation
	if (!in_array($type,$valid_resource_types)) {
		echo "Invalid resource type.";
		if ($fckphp_config['Debug']===true) recordOutput();
		exit(0);
	}
	
	require_once "Commands/$command.php";

	$action=new $command($fckphp_config,$type,$cwd);

	$action->run();
	if ($fckphp_config['Debug']===true) recordOutput();
	
} else {
	//No reason for me to be here.
	echo "Invalid command.";
	echo str_replace("\n","<br />",print_r($_GET,true));
	if ($fckphp_config['Debug']===true) recordOutput();
	exit(0);
}


function recordOutput() {
	global $fckphp_config;

	if ($fckphp_config['Debug']===true) {
		$contents=ob_get_contents();
		if (strlen($contents)>0) {
			if ($fh=fopen($fckphp_config['DebugOutput'],"w")) {
				fwrite($fh,"\n\$_GET::\n".print_r($_GET,true)."\n");
				fwrite($fh,$contents);
				fclose($fh);
			}
		}
		ob_flush();
	}
}

function outputHeaders() {

	//Anti browser caching headers
	//Borrowed from fatboy's implementation  (fatFCK@code247.com)
	
	// ensure file is never cached
	// Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	
	// HTTP/1.0
	header("Pragma: no-cache");
}
?> 
