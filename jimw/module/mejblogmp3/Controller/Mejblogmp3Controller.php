<?php
/**
 * Mejblogmp3_Mejblogmp3Controller
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/Mejblogmp3Message.php');
include_once('Model/Mejblogmp3Message/Row.php');
include_once('Model/Mejblogmp3Message/Rowset.php');
include_once('Model/Mejblogmp3Comment.php');
class Mejblogmp3_Mejblogmp3Controller extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$messages = new Mejblogmp3Message();
		$result = $messages->fetchAll(array('tree_parentid = ?' => $tree->id), 'blogmp3message_date DESC');
		$this->view->messages = $result;
		$this->view->tree = $tree;
		$this->render('blog');
	}
}
?>