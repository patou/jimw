<?php
/**
 * Jimw_Install_Action
 *
 * @author	   Patou
 * @category   Jimw_Install
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Action.php 179 2008-03-12 17:17:31Z logisim $
 */
abstract class Jimw_Install_Action extends Zend_Controller_Action
{
    public function init ()
    {
        $request = $this->getRequest();
        $tree = $request->getTree();
        $ext = $request->getParam('format', JIMW_DEFAULT_EXT);
        Zend_Registry::set('format', $ext);
        $this->getHelper('ViewRenderer')->setViewSuffix($ext);
        $this->view->request = $request;
        $this->view->path = $request->getBaseUrl() . '/public';
        $this->view->tree = $tree;
        $this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages();
        $this->initModule();
    }
    public function initModule ()
    {}
    /**
     * Translate string
     *
     * @param string $messageId
     * @param string $local
     * @return string
     */
    public function _ ($messageId, $local = null)
    {
        return $this->view->_($messageId, $local);
    }
}