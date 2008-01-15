<?php
/**
 * CommentController
 *
 * @author	   Patou
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/BlogMessage.php');
include_once('Model/BlogComment.php');
class Blog_CommentController extends Jimw_Module_Action
{
	public function addAction ()
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$comments = new BlogComment();
		$comment = $comments->fetchNew();
		$comment->username = $request->cname;
		$comment->blogmessage_id = $request->cid;
		$comment->content = $request->cmessage;
		$comment->date = new Zend_Db_Expr('NOW()');
		$comment->save(); 
		$this->_forward($request->alias, 'blog');
	}
}
?>