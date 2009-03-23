<?php
/**
 * AuthController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class AuthController extends Jimw_Admin_Action
{
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $this->_redirect('/');
    }
    public function loginAction ()
    {
        $this->view->message = '';
        $form = new Jimw_Form_LoginForm();
        if ($this->_request->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // collect the data from the user
                $values = $form->getValues();
                // setup Zend_Auth adapter for a database table
                $user = new Jimw_Site_User();
                $authAdapter = new Zend_Auth_Adapter_DbTable($user->getAdapter());
                $authAdapter->setTableName($user->getRealTableName());
                $authAdapter->setIdentityColumn('user_login');
                $authAdapter->setCredentialColumn('user_password');
                //$authAdapter->setCredentialTreatment('MD5(?)');
                // Set the input credential values to authenticate against
                Jimw_Debug::dump($values);
                $md5 = md5($values['password']);
                $authAdapter->setIdentity($values['login']);
                $authAdapter->setCredential($md5);
                // do the authentication
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // success: store database row to auth's storage
                    // system. (Not the password though!)
                    $data = $authAdapter->getResultRowObject(null, 'password');
                    $auth->getStorage()->write($data);
                    if (!empty($values['redirect']))  $this->_redirect($values['redirect'], array('prependBase'=>false));
                    else $this->_forward('index', 'index', 'default');
                    return;
                } else {
                    // failure: clear database row from session
                    $this->view->message = $this->_('Login failed.');
                }
        }
        $form->populate(array('redirect' => $this->getRequest()->getRequestURI()));
        $this->view->form = $form;
        $this->render();
    }

    function logoutAction ()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}