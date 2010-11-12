<?php
/**
 * BlogController
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/BlogMessage.php');
include_once('Model/BlogComment.php');
include_once('Model/BlogCommentForm.php');

class Blog_BlogController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->getRequest();
		$tree = $request->getTree();
		$messages = new BlogMessage();
		$result = Zend_Paginator::factory($messages->select()
		        ->where('tree_parentid = ?', $tree->id)
		        ->where('blogmessage_date <= NOW()')
		        ->order('blogmessage_date DESC'));

		// initialisation des valeurs par défaut
		$result->setPageRange(10);
                if (isset($tree->param->nbMessageByPage))
		    $result->setItemCountPerPage($tree->param->nbMessageByPage);
		elseif (isset($tree->module->config->nbMessageByPage))
		    $result->setItemCountPerPage($tree->module->config->nbMessageByPage);
		else
		    $result->setItemCountPerPage(10);
		$page = 1;
		if (isset($request->page))
		    $page = $request->page;
		$result->setCurrentPageNumber($page);

		$this->view->messages = $result;
		$form = new BlogCommentForm();
		$form->setAction($this->view->url(array('module' => 'blog', 'controller' => 'comment', 'action' => 'add', 'format' => 'phtml', 'alias' => $tree->alias), 'format'));
		$this->view->form = $form;
		$this->view->tree = $tree;
		$this->render('blog');
	}
}
?>