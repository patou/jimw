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
class Article_ArticleController extends Jimw_Module_Action
{
	public function viewModule ($alias)
	{
		
		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		$tree = $request->getTree();
//		/* @var $db Zend_Db_Adapter_Abstract */
//		$db = Zend_Registry::get('db');
//		/* @var $db Zend_Db_Adapter_Abstract */
//		$select = $db->select();
//		$select->from('jimw_article', '*');
//		$select->where('tree_id = ?', $tree['tree_id']);
//		$result = $db->fetchRow($select);
//
//		$this->getResponse()->appendBody($result['article_content']);
		$article = new Article();
		$where = $article->getAdapter()->quoteInto ('tree_id = ?', $tree['tree_id']);
		$result = $article->fetchRow($where);
		$this->view->article = $result;
		$this->view->tree = $this->_request->getTree ();
		$this->render("article", "body");
	}
}
?>