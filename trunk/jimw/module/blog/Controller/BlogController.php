<?php
/**
 * NewsController
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/BlogMessage.php');
include_once('Model/BlogMessage/Row.php');
include_once('Model/BlogMessage/Rowset.php');
include_once('Model/BlogComment.php');
class Blog_BlogController extends Jimw_Module_Action
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$messages = new BlogMessage();
		$result = $messages->fetchAll(array('tree_parentid = ?' => $tree->id));
		foreach ($result as $message) {
			$comments = new BlogComment();
			$result2 = $comments->fetchAll(array('blogmessage_id = ?' => $message->id));
			$message->setComments($result2);
		}
		$this->view->messages = $result;
		$this->view->tree = $tree;
		$this->render('blog', 'body');
	}
}
?>