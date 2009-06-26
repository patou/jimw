<?php

/**
 * SitemapController
 * 
 * @author
 * @version 
 */

class SeoController extends Jimw_Module_Action {
	/**
	 * The default action - show the home page
	 */
	public function sitemapAction() {
	}
	
	public function robotsAction() {
		$deniedpage = array('/admin', '/default/error', '/default/auth');
		//TODO add all denied page and controller
		$this->view->deniedpage = $deniedpage;
	}
	
	public function faviconAction() {
	    
	    header("Pragma: public");
	    $favicon = $this->getFavicon();
        header("Content-Type: " . $favicon['type']);
	    @readfile($favicon['file']);
	    exit;
	}

}