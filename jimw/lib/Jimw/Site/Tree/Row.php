<?php
/**
 * Jimw_Site_Tree
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Tree_Row extends Jimw_Db_Row {
	/**
	 * Return the children tree node list
	 *
	 * @return Jimw_Db_Rowset
	 */
	public function getChildrens () {
		return $this->_table->getChildrens ($this->__get('id'));
	}
	/**
	 * Return if the tree node has children
	 *
	 * @return boolean
	 */
	public function hasChildren () {
		return true;
	}
	
	/**
	 * Return the parent of the Tree node
	 *
	 * @return Jimw_Site_Tree_Row
	 */
	public function getParent () {
		return $this->_table->hasChildren ($this->__get('parent'));
	}
}
?>