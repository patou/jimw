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
	
	protected $children;
	protected $nodes;
	
	public function __construct($currentid = 0) {
		/* @var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db');
		$select = $db->select();
		$select->from('jimw_tree', '*');
		$select->from('jimw_domain', '*');
		//$select->from('jimw_site', array('site_name', 'site_path'));
		$select->where('jimw_tree.site_id = jimw_domain.site_id');
		$result = $db->fetchAll($select, array('tree_order', 'tree_alias'));
		if ($result === false) {
			return false;
		} else {
			foreach ($result as $node) {
				if (strtolower($node['domain_protocol']) == 'http' && $node['domain_port'] == 80) $node['domain_port'] = 0;
				if (strtolower($node['domain_protocol']) == 'https' && $node['domain_port'] == 443) $node['domain_port'] = 0;

				$node['linkurl'] = $node['domain_protocol'].'://';
				if (!empty($node['domain_subdomain']))
					$node['linkurl'] .= $node['domain_subdomain'].'.';
				if ($node['domain_port'] == 0)
					$node['linkurl'] .= $node['domain_name'].'/';
				else
					$node['linkurl'] .= $node['domain_name'].':'.$node['domain_port'].'/';
				if (!empty($node['domain_path']))
					$node['linkurl'] .= $node['domain_path'].'/';
				$node['linkurl'] .= $node['tree_alias'].'/';
				
				$node['active'] = ($node['tree_id'] == $currentid);
				$node['expanded'] = ($node['tree_id'] == $currentid);
				
				$this->nodes[$node['tree_id']] = $node;
				$this->children[$node['tree_parentid']][] = $node['tree_id'];
			}
			$newid = $this->nodes[$currentid]['tree_parentid'];
			while (isset($this->nodes[$newid])) {
				$this->nodes[$newid]['expanded'] = true;
				$newid = $this->nodes[$newid]['tree_parentid'];
			}
		}
	}
	
	public function getChildren ($parent = 0) {
		$list = array();
		if (isset($this->children[$parent])) {
			foreach ($this->children[$parent] as $child) {
				$list[] = $this->nodes[$child];
			}
		}
		return $list;
	}
	
	public function getMenu ($menutitle) {
		$list = array();
		/* @var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db');
		$select = $db->select();
		$select->from('jimw_menuitem', '*');
		$select->where('jimw_menuitem.menu_name = ?', $menutitle);
		$result = $db->fetchAll($select, array('menuitem_order', 'tree_id'));
		if ($result === false) {
			return $list;
		} else {
			foreach ($result as $item) {
				if ($item['menuitem_status'] == 0) {
					$list = array_merge($list, $this->getChildren($item['tree_id']));
				} else {
					$list[] = $this->nodes[$item['tree_id']];
				}
			}
			return $list;
		}
		if (array_key_exists($parent, $this->children)) {
			foreach ($this->children[$parent] as $child) {
				$list[] = $this->nodes[$child];
			}
		}
		return $list;
	}
	
	// Obsolete
	public function getChildrens ($parent = 0) 	{
		$where = $this->getAdapter()->quoteInto('tree_parentid = ?', $parent);
		return $this->fetchAll($where, array('tree_order', 'tree_alias'));
	}
	
	public function hasChildren ($parent = 0)	{
		// selection tool
		$select = $this->_db->select();

		// the FROM clause
		$select->from($this->_name, 'COUNT(*)', $this->_schema);

		// the WHERE clause
		$select->where('tree_parentid = ?', $parent);

		$select->order(array('tree_order', 'tree_alias'));

		// return the results
		$stmt = $this->_db->query($select);
		$count = $stmt->fetchColumn ();
		return $count > 0;
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
		if (empty($data['tree_alias']))
		$data['tree_alias'] = $this->getRewriteString($data['tree_menutitle']);
		if (empty($data['tree_status']))
		$data['tree_status'] = 3;
		if (empty($data['tree_type']))
		$data['tree_type'] = 0;
		if (empty($data['tree_creationdate']))
		$data['tree_creationdate'] = time();
		if (empty($data['tree_editiondate']))
		$data['tree_editiondate'] = time();
		return parent::insert($data);
	}

	public function update(array $data, $where)
	{
		// add a timestamp
		if (empty($data['tree_editiondate']))
		$data['tree_editiondate'] = time();
		return parent::update($data, $where);
	}
}
?>