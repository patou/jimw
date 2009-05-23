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
	
	public function robotAction() {
		$deniedpage = array('/admin', '/default/error/');
		//TODO add all denied page and controller
		$this->view->deniedpage = $deniedpage;
	}

}
?>

