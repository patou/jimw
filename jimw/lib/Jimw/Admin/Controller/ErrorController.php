<?php
/**
 * ErrorController
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class ErrorController extends Jimw_Admin_Action
{
    public function permsAction ()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_forward('login', 'auth', 'default');
        }
    }

    public function errorAction ()
    {
        $errors = $this->_getParam('error_handler');
        $render = 'error';
        $this->getResponse()->clearBody();
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
                } catch (Jimw_Site_Exception_AliasNotFound $e) {
                    $render = '404';
                } catch (Jimw_Site_Exception_SiteTreeNotFound $e) {
                    $render = '404';
                } catch (Jimw_Acl_Exception $e) {
                    $this->_forward('perms');
                } catch (Jimw_Admin_Exception $e) {
                    $render = 'admin-error';
                } catch (Jimw_Exception $e) {
                    $render = 'site-error';
                } catch (Zend_Exception $e) {
                    $render = 'framework-error';
                } catch (Exception $e) {
                    $render = 'php_error';
                }
                $this->view->message = $errors->exception->getMessage();
                break;
        }
        $this->render($render);
    }
}
?>