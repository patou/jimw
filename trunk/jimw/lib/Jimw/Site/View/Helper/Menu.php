<?php
/**
 * Jimw_Site_View_Helper_Menu
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Jimw_Site_View_Helper_Menu
{
	private function createMenu($name) {
		/* @var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', '*');
		$select->from('jimw_domain', '*');
		$select->where('jimw_tree.site_id = jimw_domain.site_id');
		$select->where('jimw_tree.tree_parentid = ?', $name);
		//$select->joinNatural('jimw_domain', '*');
		$select->order('tree_order ASC');
		$result = $db->fetchAll($select);
		return $result;
	}
	
	private function getMenu ($name)
	{
		return $this->createMenu($name);
	}
	
	/**
	 * Display the menu
	 *
	 * @param string $name
	 * @param boolean $display Auto render the menu with <li></li> tag
	 * @return string|Jimw_Site_Tree_Row
	 */
	public function menu ($name = 0, $display = true)
	{
		$menu = $this->getMenu ($name);
		if ($display)
			return $this->display_menu ($menu);
		else
			return $menu;
	}
	
	private function display_menu ($menu, $level = 0)
	{
		$html = '<li>';
		foreach ($menu as $item)
			$html .= '<ul>' . $menu->tree_menutitle . '</ul>' . "\n";
		$html .= '</li>';
		return $html;
	}
}