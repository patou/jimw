<?php
/**
 * UserController
 *
 * @author	   Logisim
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class UserController extends Jimw_Module_Action {
	
	public function subscribeAction()
    {
    	$this->view->message = '';
		$users = new Jimw_Site_User();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			// collect the data from the user
			$f = new Zend_Filter_StripTags();
			$login = $f->filter($request->getPost('login'));
			$password1 = $f->filter($request->getPost('password1'));
			$password2 = $f->filter($request->getPost('password2'));
			$lastname = $f->filter($request->getPost('lastname'));
			$firstname = $f->filter($request->getPost('firstname'));
			$email = $f->filter($request->getPost('email'));

			if (empty($login)) {
				$this->view->message = $this->_('Please provide a username.');
			} elseif (empty($password1)) {
				$this->view->message = $this->_('Please provide a password.');
			} elseif (empty($email)) {
				$this->view->message = $this->_('Please provide an e-mail.');
			} elseif ($password1 != $password2) {
				$this->view->message = $this->_('The provided passwords are differents.');
			} else {
			  	$result = $users->fetchRow(array('user_login = ?' => $login));
				if ($result) {
					$this->view->message = $this->_('This user already exists.');
				} else {
					$user = $users->fetchNew();
					$user->login = $login;
					$user->password = md5($password1);
					$user->status = 1;
					$user->lastname = $lastname;
					$user->firstname = $firstname;
					$user->email = $email;
					$user->save();
					$this->render('confirmsubscribe');
					
				}
			}
		}
    }
    
    public function loginAction()
    {
    	$this->view->message = '';
		
		if ($this->_request->isPost()) {
			// collect the data from the user
			$f = new Zend_Filter_StripTags();
			$username = $f->filter($this->_request->getPost('login'));
			$password = $f->filter($this->_request->getPost('password'));

			if (empty($username)) {
				$this->view->message = $this->_('Please provide a username.');
			} else {
				// setup Zend_Auth adapter for a database table
				$dbAdapter = Zend_Registry::get('db'); // @TODO correct to change user table
				$user = new Jimw_Site_User();
				$authAdapter = new Zend_Auth_Adapter_DbTable($user->getAdapter());
				$authAdapter->setTableName($user->getRealTableName());
				$authAdapter->setIdentityColumn('user_login');
				$authAdapter->setCredentialColumn('user_password');
				$authAdapter->setCredentialTreatment('MD5(?)');

				// Set the input credential values to authenticate against
				$authAdapter->setIdentity($username);
				$authAdapter->setCredential($password);

				// do the authentication
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate($authAdapter);

				if ($result->isValid()) {
					// success: store database row to auth's storage
					// system. (Not the password though!)
					$data = $authAdapter->getResultRowObject(null, 'password');
					$auth->getStorage()->write($data);
					$this->_forward('index', 'index', 'default');
					return;
				} else {
					// failure: clear database row from session
					$this->view->message = $this->_('Login failed.');
				}
			}
		}
		//$this->getHelper('ViewRenderer')->noRenderLayout()->setAppendBody(true);
		$this->render();
    }
    
	function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/');
	}
    
}
 ?>