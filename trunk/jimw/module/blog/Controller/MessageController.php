<?php
/**
 * BlogController
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: BlogController.php 198 2008-04-28 22:10:59Z patou.de.saint.steban $
 */

include_once('Model/BlogMessage.php');
include_once('Model/BlogComment.php');
include_once('Model/BlogCommentForm.php');

class Blog_MessageController extends Jimw_Module_Action
{
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {    // TODO Auto-generated MessageController::indexAction() default action
    }

    public function viewAction ()
    {    
        $form = new BlogCommentForm();
        $messages = new BlogMessage();
        $message = $messages->find($this->getRequest()->mid)->current();
        $form->setAction($this->view->url(array('action' => 'add', 'controller' => 'comment'), 'format'));
        $form->populate(array('blogmessage_id' => $this->getRequest()->mid));
        $this->view->form = $form;
        $this->view->message = $message;
    }
}
