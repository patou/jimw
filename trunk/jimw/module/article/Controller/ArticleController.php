<?php
/**
 * ArticleController
 *
 * @author	    Patou
 * @category   Jimw_Article
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/Article.php');
class Article_ArticleController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		$article = new Article();
		$result = $article->fetchRow(array('tree_id = ?' => $tree->id));
		$this->view->article = $result;
		$this->view->tree = $tree;
		$this->render('article', 'body');
	}
}
?>