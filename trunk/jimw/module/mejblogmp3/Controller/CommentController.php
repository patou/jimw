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

include_once('Model/Mejblogmp3Message.php');
include_once('Model/Mejblogmp3Comment.php');
class Mejblogmp3_CommentController extends Jimw_Module_Action
{
  public function addAction ()
  {
    $request = $this->_request;
    $tree = $request->getTree();
    $messages = new Mejblogmp3Message();
    $message = $messages->find($request->cid);
    if ($tree->param->comment || (count($message) && $message->current()->comment)) {
      $comments = new Mejblogmp3Comment();
      $comment = $comments->fetchNew();
      $comment->username = $request->cname;
      $comment->blogmessage_id = $request->cid;
      if (is_numeric($request->crating)) {
	  	 if ($request->crating >= 0 && $request->crating <= 5)
		   $comment->rating = $request->crating;
		 else
		   $comment->rating = -1;
	  } else $comment->rating = -1;
      $comment->content = $request->cmessage;
      $comment->date = $comment->getFormatedDate(Zend_Date::now());//new Zend_Db_Expr('NOW()');
      $comment->save();
    }
    else {
      throw new Jimw_Exception('Comment is not allowed');
    }
    $this->_forward($request->alias, 'blog');
  }
}
?>