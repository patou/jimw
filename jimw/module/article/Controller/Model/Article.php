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
class Article extends Jimw_Db_Table
{
	protected $_name = 'article';

	/**
	 * Find an article by te tree_id field
	 *
	 * @param int $id
	 * @return Jimw_Site_Tree_Rowset
	 */
	public function findByTree($id) {
		return $this->fetchRow($this->select()->where('tree_id = ?', $id)->order('article_version DESC')->limit(1));
	}
}
?>