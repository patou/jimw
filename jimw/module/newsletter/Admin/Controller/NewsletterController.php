<?php
/**
 * indexController.php
 *
 * @author	   Logisim
 * @category   Blog_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Newsletter_NewsletterController extends Jimw_Admin_Action
{
	public function newsletterAction () {
		//Jimw_Debug::dump($this->getRequest()->getParams());
		$form = new newsletter_Admin_Form_NewsletterForm();
		$req = $this->getRequest();
		$form->setAction($this->view->url(array('action' => 'newsletter', 'module' => 'newsletter', 'controller' => 'newsletter', 'alias' => $req->getParam('alias'), 'id' => $req->getParam('id')), 'format', true));
		if ($req->isPost() && $form->isValid($req->getPost())) {
			$newsletters = new newsletter_Model_Newsletter();
			$values = $form->getValues();
			$newsletter = $newsletters->fetchNew();
			$newsletter->message = $values['message'];
			$newsletter->title = $values['title'];
			$newsletter->tree_alias = $req->getParam('alias');
			$newsletter->send = 0;
			$newsletter->draft = 0;
			$newsletter->senddate = null;
			$newsletter->save();
			
			$this->_forward('send', 'newsletter', 'newsletter', array('alias' => $req->getParam('alias'), 'tree_id' => $req->getParam('id'), 'id' => $newsletter->id));
			return;
		}
		$this->view->form = $form;
		$this->render('form');
	}
	
	public function sendAction() {
		$req = $this->getRequest();
		$id = $req->id;
		$newsletters = new newsletter_Model_Newsletter();
		$newsletter = $newsletters->find($id)->current();
		$users = new newsletter_Model_Newsletteruser();
		$select = $users->select()->where('tree_alias = ?', $newsletter->tree_alias)->where('newsletteruser_lastsend != ?', $id);//->where('newsletteruser_valid = 1');
		$listusers = $users->fetchAll($select);
		$number = 0;
		foreach ($listusers as $user) {
			$mail = new Zend_Mail();
			$mail->addTo($user->email);
			$mail->setSubject($newsletter->title);
			$mail->setBodyHtml($newsletter->message);
			$mail->send();
			$user->lastsend = $id;
			$user->save();
			$number++;
		}
		if ($number > 0) {
			$newsletter->senddate = Zend_Date::now();
			$newsletter->send = $number;
			$newsletter->save();
		}
		$this->view->number_send_mail = $number;
	}
}
?>