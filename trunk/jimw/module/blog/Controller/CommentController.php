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
class Blog_CommentController extends Jimw_Module_Action
{
  public function addAction ()
  {
    $request = $this->getRequest();
    $tree = $request->getTree();
    $messages = new BlogMessage();
    $message = $messages->find($request->cid);
    if ($tree->param->comment || (count($message) && $message->current()->comment)) {
        if (defined('JIMW_AKISMET_KEY') && defined('JIMW_AKISMET_URL')) {
            $akismet = new Zend_Service_Akismet(JIMW_AKISMET_KEY, JIMW_AKISMET_URL);
            $data = array(
    			'user_ip'              => $_SERVER['REMOTE_ADDR'],
    			'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
    			'comment_type'         => 'comment',
    			'comment_author'       => $request->cname,
    			'comment_content'      => $request->cmessage
            );
            if ($akismet->isSpam($data)) {
                throw new Jimw_Exception('Sorry, but we think you\'re a spammer.');
            }
        }
        $comments = new BlogComment();
        $comment = $comments->fetchNew();
        $comment->username = $request->cname;
        $comment->blogmessage_id = $request->cid;
        $comment->content = $request->cmessage;
        $comment->date = Zend_Date::now()->getIso();//new Zend_Db_Expr('NOW()');
        $comment->save();
    }
    else {
      throw new Jimw_Exception('Comment is not allowed');
    }
    $this->_forward($request->alias, 'blog');
  }
}
?>