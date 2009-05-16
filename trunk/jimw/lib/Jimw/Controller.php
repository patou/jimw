<?php
/** * Jimw_Controler * * @author	    Patou * @category   Jimw_Core * @package    Jimw * @copyright  Copyright (c) 2006-2007 jimw.fr * @license    http://www.jimw.fr * @version    $Id$ */
class Jimw_Controller
{
    /**
     * The request var
     *
     * @var $request Jimw_Global_Request
     */
    protected $request;
    /**
     * The responce var
     *
     * @var $response Zend_Controller_Response_Http
     */
    protected $response;
        /**
     * The Dispatch var
     *
     * @var Jimw_Admin_Dispatch
     */
    protected $dispatch;
    /**
     * The global router
     *
     * @var Jimw_Global_Router
     */
    protected $router;
    protected $frontcontroller;
    /**
     * @var $view Jimw_Site_View
     */
    protected $view;
    /**
     *
     * @var $translate Zend_Translate_Adapter
     */
    protected $translate;

    /**
     * Get the request var
     *
     * @var Jimw_Global_Request
     */
    public function getRequest ()
    {
        return $this->request;
    }
    /**
     * Get the request var
     *
     * @var Zend_Controller_Response_Http
     */
    public function getResponse ()
    {
        return $this->response;
    }

    public function initTranslate ()
    {
        Zend_Locale::setDefault(JIMW_LANG);
        try {
            $locale = new Zend_Locale('auto');
        } catch (Zend_Locale_Exception $e) {
            $locale = new Zend_Locale(JIMW_LANG);
        }
        $options = array('scan' => Zend_Translate::LOCALE_DIRECTORY);
        if (!JIMW_DEBUG_MODE)
            $options['disableNotices'] = true; //Disable notices on production mode
        $trans = new Zend_Translate('array', JIMW_REP_LANG, null, $options);
        //if (!$trans->isAvailable($locale)) $locale = Zend_Locale::getDefault();
        //Jimw_Debug::dump($locale);
        $trans->setLocale($locale);
        Zend_Form::setDefaultTranslator($trans);
        $this->translate = $trans;
        Zend_Registry::set('Zend_Translate', $trans);
        Zend_Registry::set('Zend_Locale', $locale);
        return $trans;
    }

    public function initView ()
    {
        $view = new Jimw_Site_View();
        $view->addHelperPath(JIMW_REP_LIB . 'Zym/View/Helper', 'Zym_View_Helper');
        $view->addFilterPath(JIMW_REP_LIB . 'Zym/View/Filter', 'Zym_View_Filter');
        $view->addHelperPath(JIMW_REP_LIB . 'Jimw/view/helpers', 'Jimw_View_Helper');
        $this->initTranslate();
        //$view->setTranslate();
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
    		'paginator.phtml'
        );
        $viewRenderer = new Jimw_Site_View_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        $this->view = $view;
        return $view;
    }

    public function initCache()
    {
        if (!JIMW_DEBUG_MODE) { // Do not cache in debug mode
            //TODO Do cache for everything (db, translate, page, module...)
        }

    }
}