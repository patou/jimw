<?php
/**
 * AlbumController
 *
 * @author	    Patou
 * @category   Jimw_Picasa
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: GalleryController.php 205 2008-05-12 14:00:51Z patou.de.saint.steban $
 */


class Picasa_AlbumController extends Jimw_Module_Action
{
	public function viewAction(){
		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$tree = $request->getTree();
		$albumid = $request->idAlbum;
		/* @var $tree Jimw_Site_Tree_Row */
		$albums = array();
		$photos = array();
		
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
		
		// Liste des photos de l'album 
		$query = $service->newAlbumQuery();
		
		$query->setUser("default");
		$query->setAlbumName($albumid);
		
		$albumFeed = $service->getAlbumFeed($query);
		
		foreach ($albumFeed as $albumEntry) {
			//print_r($albumEntry);die;
			//$photo["url"] = $albumEntry->mediaGroup->content[0]->url;
			$mediaContentArray = $albumEntry->getMediaGroup()->getContent();
  			$photo["url"] = $mediaContentArray[0]->getUrl();
  			
  			
		    $photo["title"] =  $albumEntry->title->text;
		    $photo["thumbnails"] = $albumEntry->mediaGroup->thumbnail[1]->url;
		    $photo["thumbnails_width"] = $albumEntry->mediaGroup->thumbnail[1]->width;
		    $photo["thumbnails_height"] = $albumEntry->mediaGroup->thumbnail[1]->height;
		    
		    $photos[] = $photo;
		}
		
		$this->view->albums = $albums;
		$this->view->photos = $photos;
		$this->render('photos');
	}
}
