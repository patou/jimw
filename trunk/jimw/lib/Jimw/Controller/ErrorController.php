<?php
/**
 * IndexController
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class ErrorController extends Jimw_Admin_Action {
	public function errorAction()
    {
		$this->error();
    }
    
    public function error()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->view->message = $this->_("This page didn't exist");

                // ... get some output to display...
                break;
            default:
                // application error; display error page, but don't change
                // status code
                
                $this->view->message = $this->_($errors->exception->getMessage ()) . Jimw_Debug::display_exception($errors->exception, false);
                break;
        }
    }
}
 ?>