<?php
/**
 * ArticleController
 *
 * @author	    Patou
 * @category   Jimw_Article
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */


class Gallery_GalleryzipController extends Jimw_Module_Action
{
	public function viewModule ($alias)
	{

		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$files = $request->files;
		$dir = $request->dir;
		$zipfile = new Jimw_Zip();

		// add the subdirectory ... important!
		$zipfile -> addDirectory($alias . "/");

		foreach ($files as $file) {
		// add the binary data stored in the string 'filedata'
			$filedata = file_get_contents($dir . '/' . $file);
			$zipfile -> addFile($filedata, $alias.'/'.$file);
		}
		// the next three lines force an immediate download of the zip file:
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=".$alias.".zip");
		echo $zipfile -> getZippedfile();
		exit();
	}
}
?>