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
class Jimw_Site_Tree extends Jimw_Db_Table {
	protected $_name = 'tree';
	protected $_rowClass = 'Jimw_Site_Tree_Row';

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