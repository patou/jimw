<?php
/**
 * Jimw_Site_Plugins_GlobalRender
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Admin_GlobalRender extends Zend_Controller_Plugin_Abstract
{
	private $view;
		
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $this->view = new Zend_View ();
        $this->view->path = $request->getBaseUrl() . '/public';
        $this->view->request = $request;
		$this->view->setScriptPath(JIMW_REP_LIB . 'Jimw/Admin/views');
        $response->prepend('header', $this->view->render('header.phtml'));
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $response->append('footer', $this->view->render('footer.phtml'));
    }
}
?>