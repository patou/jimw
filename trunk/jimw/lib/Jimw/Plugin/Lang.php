<?php

class Jimw_Plugin_Lang extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $locale = Zend_Registry::get('Zend_Locale');
        $session = new Zend_Session_Namespace();
        if (isset($session->Zend_Locale))
        {
            $locale = $session->Zend_Locale;
        }
        if (isset($request->jimw_locale)) {
            if ($request->jimw_locale == 'none') {
                $locale = Zend_Registry::get('Zend_Locale');
                unset($session->Zend_Locale);
            }
            elseif ($translate->isAvailable($request->jimw_locale)) {
                $locale = new Zend_Locale($request->jimw_locale);
                $session->Zend_Locale = $locale;
            }
        }
        $translate->setLocale($locale);
        Zend_Registry::set('Zend_Locale', $locale);
    }
}
?>