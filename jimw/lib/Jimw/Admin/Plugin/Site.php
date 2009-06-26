<?php
/**
 *
 */
class Jimw_Admin_Plugin_Site extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        /*
        //Jimw_Debug::dump($request);
        $session = new Zend_Session_Namespace('Admin');
        // If the param site is set, change the session site.
        $sites = new Jimw_Site_Site();
        if (isset($request->site)) {
            $session->site = $sites->find($request->site);
        }
        if (! isset($session->site)) {
            $session->site = Zend_Registry::get('site');
        }*/
    }
}
