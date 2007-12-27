<?php
/**
 * BlogMessage
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class BlogMessage extends Jimw_Db_Table
{
	protected $_name = 'blogmessage';

	/**
	 * Find all blog messages by the tree_id field
	 *
	 * @param int $id
	 * @return Jimw_Site_Tree_Rowset
	 */
	public function findByTree($id) {
		return $this->fetchAll(array('tree_id = ?' => $id));
	}
	
}
?>