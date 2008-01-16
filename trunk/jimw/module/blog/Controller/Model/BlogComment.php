<?php
/**
 * BlogComment
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class BlogComment extends Jimw_Db_Table
{
	protected $_name = 'blogcomment';
	protected $_dependentTables = array('BlogMessage');
	protected $_referenceMap    = array(
	'blog' => array(
	'columns'           => array('blogmessage_id'),
	'refTableClass'     => 'BlogMessage',
	'refColumns'        => 'blogmessage_id',
	'onDelete' 			=> 'cascade',
	'onUpdate' 			=> 'cascade',
	));
	/**
	 * Find all blog comments by the blogmessage_id field
	 *
	 * @param int $id
	 * @return Jimw_Db_Rowset
	 */
	public function findByBlogMessage($id) {
		return $this->fetchAll(array('blogmessage_id = ?' => $id));
	}
}
