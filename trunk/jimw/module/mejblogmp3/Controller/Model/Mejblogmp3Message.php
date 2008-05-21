<?php
/**
 * Mejblogmp3Message
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
include_once (dirname(__FILE__) . '/Mejblogmp3Message/Row.php');
include_once (dirname(__FILE__) . '/Mejblogmp3Message/Rowset.php');
class Mejblogmp3Message extends Jimw_Db_Table
{
	protected $_name = 'blogmp3message';
	protected $_dependentTables = array('Mejblogmp3Comment');
	
	/**
	 * Find all blog messages by the tree_id field
	 *
	 * @param int $id
	 * @return Mejblogmp3Message_Row
	 */
	public function findByTree($id) {
		return $this->fetchAll(array('tree_id = ?' => $id));
	}
	
}
?>