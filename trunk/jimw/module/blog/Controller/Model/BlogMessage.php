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
include_once (dirname(__FILE__) . '/BlogMessage/Row.php');
include_once (dirname(__FILE__) . '/BlogMessage/Rowset.php');
class BlogMessage extends Jimw_Db_Table
{
	protected $_name = 'blogmessage';
	protected $_rowClass = 'BlogMessage_Row';
	protected $_rowsetClass = 'BlogMessage_Rowset';

	/**
	 * Find all blog messages by the tree_id field
	 *
	 * @param int $id
	 * @return BlogMessage_Row
	 */
	public function findByTree($id) {
		return $this->fetchAll(array('tree_id = ?' => $id));
	}
	
}
?>