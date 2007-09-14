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

	public function __construct(array $config = array ()) {
		if (isset($config['data']['active']))
		$this->_active = true;
		if (isset($config['data']['expanded']))
		$this->_expanded = true;
		parent::__construct($config);
	}
	/**
	 * Set if the current Row is the current page
	 *
	 * @var boolean
	 */
	protected $_active = false;

	public function getActive () {
		return $this->_active;
	}

	public function setActive ($active) {
		$this->_active = $active;
	}

	/**
	 * Set if the current Page is the curent page or his children
	 *
	 * @var boolean
	 */
	protected $_expanded = false;

	public function getExpanded () {
		return $this->_expanded;
	}

	public function setExpanded ($expanded) {
		$this->_expanded = $expanded;
	}

	/**
	 * Get the url of the tree
	 *
	 * @return string
	 */
	public function getUrl () {
		return Jimw_Site_Domain::toUrl($this->site_id, $this->alias);
	}

	/**
	 * Get the url of the tree
	 *
	 * @return string
	 */
	public function toUrl () {
		return $this->getUrl ();
	}
	/**
	 * Return the children tree node list
	 *
	 * @return Jimw_Db_Rowset
	 */
	public function getChildren () {
		return $this->_table->getChildren ($this->id);
	}
	/**
	 * Return if the tree node has children
	 *
	 * @return boolean
	 */
	public function hasChildren () {
		return $this->_table->hasChildren ($this->id);
	}

	/**
	 * Return the parent of the Tree node
	 *
	 * @return Jimw_Site_Tree_Row
	 */
	public function getParent () {
		return $this->_table->find ($this->parentid);
	}
}
?>