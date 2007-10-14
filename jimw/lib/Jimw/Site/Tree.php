<?php
/**
 * Jimw_Site_Tree
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Tree extends Jimw_Db_Table {
	protected $_name = 'tree';
	protected $_rowClass = 'Jimw_Site_Tree_Row';

	protected $_dependentTables = array('Jimw_Site_Site', 'Jimw_Site_Module');
	protected $_referenceMap    = array(
	'site' => array(
	'columns'           => array('site_id'),
	'refTableClass'     => 'Jimw_Site_Site',
	'refColumns'        => 'site_id'
	),
	'module' => array(
	'columns'           => array('module_id'),
	'refTableClass'     => 'Jimw_Site_Module',
	'refColumns'        => 'module_id'
	));

	protected static $_children = null;
	protected static $_nodes = null;
	protected static $_currentid = -1;

	/**
	 * Set the current page tree
	 *
	 * @param int $currentid
	 */
	public static function setCurrent($currentid) {
		self::$_currentid = $currentid;
	}


	/**
	 * Get the current page tree
	 *
	 * @return int
	 */
	public static function getCurrent() {
		return self::$_currentid;
	}

	/**
	 * Load the all tree into the class and keep it in the class
	 *
	 */
	private function _loadTree () {
		if (self::$_children === null || self::$_nodes === null) {
			$result = $this->_fetch(null, array('tree_order', 'tree_alias'));
			if ($result === false) {
				return;
			} else {
				foreach ($result as $node) {
					self::$_nodes[$node['tree_id']] = $node;
					self::$_children[$node['tree_parentid']][] = $node['tree_id'];
				}
				if (self::$_currentid >= 0) {
					$newid = self::$_nodes[self::$_currentid]['tree_parentid'];
					self::$_nodes[self::$_currentid]['expanded'] = true;
					self::$_nodes[self::$_currentid]['active']  = true;
					while (isset(self::$_nodes[$newid])) {
						self::$_nodes[$newid]['expanded'] = true;
						$newid = self::$_nodes[$newid]['tree_parentid'];
					}
				}
			}
		}
	}

	public function getChildren ($parent = 0) {
		$this->_loadTree();
		$list = array();
		if (isset(self::$_children[$parent])) {
			foreach (self::$_children[$parent] as $child) {
				$list[] = self::$_nodes[$child];
			}
		}
		$data = array(
		'table'   => $this,
		'data'    => $list,
		'stored'  => true,
		'rowClass' => $this->_rowClass
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);
		//return $list;
	}


	/**
	 * Test if the Tree id given has children
	 *
	 * @param int $parent
	 * @return boolean
	 */
	public function hasChildren ($parent = 0)	{
		$this->_loadTree();
		return (isset(self::$_children[$parent]));
	}

	/**
	 * Create the alias name if not exist
	 *
	 * @param string $sString
	 * @return string
	 */
	private function getRewriteString($sString)
	{
		$string = strtolower( htmlentities( strtr($sString, array("?"=>"Ae", "?"=>"Ue", "?"=>"Oe", "?"=>"ae", "?"=>"ue", "?"=>"oe")) ) );
		$string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
		$string = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($string));
		$string = trim($string, "-");
		return $string;
	}

	public function insert(array $data)
	{
		// add a timestamp
		if (empty($data['tree_pagetitle']) && !empty($data['tree_menutitle'])) {
			$data['tree_pagetitle'] = $data['tree_menutitle'];
		} elseif (empty($data['tree_menutitle']) && !empty($data['tree_pagetitle'])) {
			$data['tree_menutitle'] = $data['tree_pagetitle'];
		}
		if (empty($data['tree_alias'])) {
			$data['tree_alias'] = $this->getRewriteString($data['tree_menutitle']);
		}
		if (empty($data['tree_status'])) {
			$data['tree_status'] = 3;
		}
		if (empty($data['tree_type'])) {
			$data['tree_type'] = 0;
		}
		if (empty($data['tree_creationdate'])) {
			$data['tree_creationdate'] = time();
		}
		if (empty($data['tree_editiondate'])) {
			$data['tree_editiondate'] = time();
		}
		return parent::insert($data);
	}

	/**
	 * Override the default createRow to add some default values
	 *
	 * @param array $data
	 * @return Jimw_Db_RowClass
	 */
	public function createRow(array $data = array())
	{
		$data['tree_creationdate'] = time();
		$data['tree_editiondate'] = time();
		$data['tree_type'] = 0;
		$data['tree_status'] = 3;
		return parent::createRow($data);
	}

	public function update(array $data, $where)
	{
		// add a timestamp
		if (empty($data['tree_editiondate'])) {
			$data['tree_editiondate'] = time();
		}
		return parent::update($data, $where);
	}

	/**
	 * Find a Tree item or a list of tree item
	 *
	 * @param int|array $id
	 * @return Jimw_Db_Rowset
	 */
	public function find () {
		$args = func_get_args();
		$this->_loadTree();
		$list = array();
		foreach ($args as $i) {
			if (isset(self::$_nodes[$i])) {
				$list[] = self::$_nodes[$i];
			}
		}
		$data  = array(
		'table'    => $this,
		'data'     => $list,
		'rowClass' => $this->_rowClass,
		'stored'   => true
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);
	}

	/**
	 * Find a Tree Item by the , and get the module and Site
	 *
	 * @param string $alias
	 * @return boolean|Jimw_Site_Tree_Row
	 */
	public function findByAlias($alias) {
		$list = $this->_fetch(array('tree_alias = ?' => $alias), array('tree_version DESC'));
		if ($list) {
			$list = $list[0];
			$module = new Jimw_Site_Module();
			$list['module'] = $module->find($list['module_id'])->current ();
			$site = new Jimw_Site_Site();
			$list['site'] = $site->find($list['site_id'])->current ();
			$data  = array(
			'table'    => $this,
			'data'     => $list,
			'rowClass' => $this->_rowClass,
			'stored'   => true
			);
			Zend_Loader::loadClass($this->_rowClass);
			return new $this->_rowClass($data);
		}
		return false;
	}

	/**
	 * Find a Tree Item by the , and get the module and Site
	 *
	 * @param string $alias
	 * @return boolean|Jimw_Site_Tree_Row
	 */
	public function findBySite($site) {
		$this->_loadTree();
		$id = $site->tree_id;
		if (isset(self::$_nodes[$id])) {
			$list = self::$_nodes[$id];
			$module = new Jimw_Site_Module();
			$list['module'] = $module->find($list['module_id'])->current ();
			$list['site'] = $site;
			$data  = array(
			'table'    => $this,
			'data'     => $list,
			'rowClass' => $this->_rowClass,
			'stored'   => true
			);
			Zend_Loader::loadClass($this->_rowClass);
			return new $this->_rowClass($data);
		}
		return false;
	}
}
?>