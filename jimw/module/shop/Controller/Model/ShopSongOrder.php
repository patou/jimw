<?php
/**
 * ShopSong
 *
 * @author	   Logisim
 * @category   Jimw_Shop
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: $
 */
class ShopSongOrder extends Jimw_Db_Table
{
	protected $_name = 'shopsongorder';
	
	/**
	 * Find all JIMW Shop songs by the shopalbum_id field
	 *
	 * @param int $id
	 * @return ShopSong_Rowset
	 */
	/*public function findByAlbum($id) {
		return $this->fetchAll(array('shopalbum_id = ?' => $id), 'shopsong_id');
	}*/
	
}
?>