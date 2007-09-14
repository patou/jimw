<?php
/**
 * Jimw_Site_Menu
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Menu extends Jimw_Db_Table {
	protected $_name = 'menuitem';

	/**
	 * Get a menu from menu name
	 *
	 * @param string $name
	 * @return Jimw_Db_Rowset|boolean
	 */
	public function getMenu($name) {
		return $this->fetchAll(array('menu_name = ?' => $name), array('menuitem_order', 'tree_id'));
	}
}