<?php
/**
 * CommentController
 *
 * @author	   Patou
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/BlogMessage.php');
include_once('Model/BlogComment.php');
include_once('Model/BlogCommentForm.php');
class Blog_CommentController extends Jimw_Module_Action
{
  public function addAction ()
  {
    $request = $this->getRequest();
    $tree = $request->getTree();
    $form = new BlogCommentForm();
    $messages = new BlogMessage();
    $message = $messages->find($request->blogmessage_id)->current();
    if ($request->isPost() && $form->isValid($request->getPost())) {
        if ($message && $message->comment) {
            $values = $form->getValues();
            if (defined('JIMW_AKISMET_KEY') && defined('JIMW_AKISMET_URL')) {
                try {
                    $akismet = new Zend_Service_Akismet(JIMW_AKISMET_KEY, JIMW_AKISMET_URL);
                    $data = array(
            			'user_ip'              => $_SERVER['REMOTE_ADDR'],
            			'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
            			'comment_type'         => 'comment',
            			'comment_author'       => $values['blogcomment_username'],
            			'comment_content'      => $values['blogcomment_content']
                    );
                    if ($akismet->isSpam($data)) {
                        throw new Jimw_Exception('Sorry, but we think you\'re a spammer.');
                    }
                }
                catch (Exception $e) {
                    Jimw_Debug::display_exception($e);
                }
            }
            $comments = new BlogComment();
            $comment = $comments->fetchNew();
            $comment->username = $values['blogcomment_username'];
            $comment->blogmessage_id = $values['blogmessage_id'];
            $comment->content = $values['blogcomment_content'];
            $comment->date = Zend_Date::now();//new Zend_Db_Expr('NOW()');
            $comment->save();
            $this->_redirect($this->view->url(array('action' => 'view', 'controller' => 'message', 'alias' => $tree->alias, 'mid' => $values['blogmessage_id']),'format'));
        }
        else {
          throw new Jimw_Exception('Comment is not allowed');
        }
    }
    $form->populate(array('blogmessage_id' => $request->cid));
    $this->view->form = $form;
    $this->view->message = $message;
  }
}
?>