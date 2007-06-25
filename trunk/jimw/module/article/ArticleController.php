<?php
/**
 * ArticleController
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:  $
 */
 
class Article_ArticleController extends Jimw_Module_Action
{
	public function index () {
		$this->getResponse()->appendBody('Article');
	}
	
	public function viewModule ($alias)
	{
		/* @var $request Jimw_Global_Request */
		$request = $this->_request;
		/* @var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_article', '*');
		$tree = $request->getTree();
		$select->where('tree_id = ?', $tree['tree_id']);
		$result = $db->fetchRow($select);

		$this->getResponse()->appendBody($result['article_content']);
	}
}
?>