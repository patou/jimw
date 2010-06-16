<?php
/**
 * NewsletterController
 * Module to create web form
 * @author	    Patou
 * @category   Jimw_Form
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */


class Newsletter_NewsletterController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
	    $req = $this->getRequest();
	    $tree = $req->getTree();
		$form = new newsletter_Form_NewsletterForm();
		if ($req->isPost() && $form->isValid($req->getPost())) {
		    $this->validForm($form, $tree);
		}
		else {
		    $form->setAction($this->view->url(array('alias' => $tree->alias), 'alias'));
		    $this->view->form = $form;
		    $this->render('form');
		}
	}

	protected function validForm(Jimw_Form $form, $tree) {
	    $db = new newsletter_Model_Newsletteruser();
	    $user = $db->fetchNew();
        $user->tree_alias = $tree->alias;
        $user->email = $form->getValue('email');
        $user->lastsend = 0;
        $user->valide = 0;
        $user->save();
        $this->view->success_text = $tree->param->get('sucess_text', $this->_("Thanks you to register to the newsletter"));
	    $this->render('formSuccess');
	}
}
