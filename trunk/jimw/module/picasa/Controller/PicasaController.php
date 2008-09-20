<?php
/**
 * PicasaController
 *
 * @author	    Manu
 * @category   Jimw_Picasa
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: GalleryController.php 205 2008-05-12 14:00:51Z patou.de.saint.steban $
 */


class Picasa_PicasaController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{

		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$tree = $request->getTree();
		/* @var $tree Jimw_Site_Tree_Row */
		$albums = array();
		
		// Parameters for ClientAuth authentication
		$service = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
		$username = $tree->getParam('username_picasa');
		$password = $tree->getParam('password_picasa');
		
		// Create an authenticated HTTP client
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);

		// Create an instance of the service
		$service = new Zend_Gdata_Photos($client);
		
		// List des albums 
		try {
		    $userFeed = $service->getUserFeed("default");
		    foreach ($userFeed as $userEntry) {
		    	if(!$userEntry->gphotoAccess->text == "private")continue;
		    	//print_r($userEntry);
		        $albums[] =  array("text"=>$userEntry->title->text,
		        				 "thumbnail"=>$userEntry->mediaGroup->thumbnail[0]->url,
		        				 "albumId"=>$userEntry->gphotoName->text);     
		        
		    }
		} catch (Exception $e) {
		    echo "Error: " . $e->getMessage() . "<br />\n"; 
		}
		
		
		/*
		$dir = rtrim('./' . $tree->site->path, '/') . '/' . $rep;
		$base_path = $this->view->path_public . '/' . $rep . '/';
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($img = readdir($dh)) !== false) {
					if ($img != '.' && $img != '..' && !is_dir($dir . '/' . $img)) {
					    $image = new Jimw_Image($dir . '/' . $img, $base_path);
					    if ($image->hasThumbnails(true)) {
					        $size = getimagesize($image->getThumbnailsFilename());
							$photos[] = array ('url' => $image->getUrl(), 'title' => '', 'thumbnails' => $image->getThumbnailsUrl(), 'thumbnails_width' => $size[0], 'thumbnails_height' => $size[1], 'file' => $image->getFilename());
						}
					}
				}
				closedir($dh);
			}
		}
		else
			exit ("$dir isn't a valid directory");
		$this->view->photos = $photos;
		$this->view->photos_path = $dir;
		$this->render('gallery');
	}
	*/
		$this->view->albums = $albums;
		$this->render('picasa');
	}

}
