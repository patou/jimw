<?php
/**
 * ErrorController
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ErrorController.php 179 2008-03-12 17:17:31Z logisim $
 */
class ErrorController extends Jimw_Admin_Action {
	public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
		$render = 'error';
		$this->view->exception = $errors->exception;
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->view->message = $this->_("This page doesn't exist");
				$render = '404';
                // ... get some output to display...
                break;
            default:
                // application error; display error page, but don't change
                // status code
                try {
                	throw $errors->exception;
                }
                catch (Jimw_Site_Exception_AliasNotFound $e) {
                	$render = '404';
                }
        		catch (Jimw_Site_Exception_SiteTreeNotFound $e) {
                	$render = '404';
                }
        		catch (Jimw_Admin_Exception $e) {
                	$render = 'admin-error';
                }
                catch (Jimw_Exception $e) {
                	$render = 'site-error';
                }
        		catch (Zend_Exception $e) {
                	$render = 'framework-error';
                }
        		catch (Exception $e) {
                	$render = 'php-error';
                }
                $this->view->message = $errors->exception->getMessage ();
                break;
        }
        $this->render($render);
    }
}
 ?>