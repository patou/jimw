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
class Jimw_Site_Plugins_GlobalRender extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $view = new Zend_View();
        $view->setScriptPath('./public/template/');
		$view->request = $request;
        $response->prepend('header', $view->render('header.phtml'));
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $view = new Zend_View();
		$view->setScriptPath('./public/template/');
        $response->append('footer', $view->render('footer.phtml'));
    }
}
?>