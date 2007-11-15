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
		if (isset($config['data']['active'])) {
			$this->_active = true;
			unset($config['data']['active']);
		}
		if (isset($config['data']['expanded'])) {
			$this->_expanded = true;
			unset($config['data']['expanded']);
		}
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
	 * @var array|null Unserialise param field
	 */
	protected $_param = null;

	/**
	 * Unserialize the tree_param field and save it in the $_param var.
	 *
	 */
	protected function _loadParam () {
		if ($this->_param === null) {
			$this->_param = unserialize($this->param);
		}
	}

	/**
	 * Unserialize the tree_param field and save it in the $_param var.
	 *
	 */
	protected function _saveParam () {
		if ($this->_param !== null) {
			$this->param = serialize($this->_param);
		}
	}

	/**
	 * Get a specific value of the param table
	 * If the value didn't exist the return value is the boolean false
	 *
	 * @param string $name
	 * @return mixed|boolean
	 */
	public function getParam($name, $default = false) {
		$this->_loadParam();
		if (isset($this->_param[$name])) {
			return $this->_param[$name];
		} else {
			return $default;
		}
	}

	/**
	 * Get the complet param field
	 *
	 * @return array
	 */
	public function getParams() {
		$this->_loadParam();
		return $this->_param;
	}


	/**
	 * Set a specific value of the param table
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value) {
		$this->_loadParam();
		$this->_param[$name] = $value;
		$this->_saveParam();
	}

	/**
	 * Set the complet param field by merging with the last param field
	 *
	 * @return array
	 */
	public function setParams(array $values) {
		$this->_loadParam();
		array_merge($this->_param, $values);
		$this->_saveParam();
	}


	/**
	 * Get the url of the tree
	 * @deprecated 
	 * @return string
	 */
	public function getUrl () {
		return Jimw_Site_Domain::toUrl($this->site_id, $this->alias);
	}

	/**
	 * Get the url of the tree
	 * @deprecated 
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