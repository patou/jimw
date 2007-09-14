<?php
/**
 * Jimw_Site_Tree_Rowset
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Tree_Rowset extends Zend_Db_Table_Rowset
{
	/**
	 * Add new rows data in the end of the actual row
	 *
	 * @param Jimw_Db_Rowset|array $rows
	 */
	public function AddRows($rows) {
		if ($rows instanceof Jimw_Db_Rowset)
			$rows = $rows->toArray ();
		array_merge($this->_data, $rows);
	}


	/**
	 * Add a new row data in the end of the actual row
	 *
	 * @param Jimw_Db_Rowset|array $row
	 */
	public function AddRow($row) {
		if ($row instanceof Jimw_Db_Row)
			$row = $row->toArray ();
		array_push ($this->_data, $row);
	}
}