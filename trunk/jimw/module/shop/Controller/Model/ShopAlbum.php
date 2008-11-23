<?php
/**
 * ShopAlbum
 *
 * @author	   Logisim
 * @category   Jimw_Shop
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: $
 */
class ShopAlbum extends Jimw_Db_Table
{
	protected $_name = 'shopalbum';
	
	/**
	 * Find a JIMW shop album by the tree_id field
	 *
	 * @param int $id
	 * @return ShopAlbum_Rowset
	 */
	public function findByTree($id) {
		return $this->fetchAll(array('tree_id = ?' => $id));
	}
	
}
?>