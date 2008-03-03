<?php
/**
 * MejDoc
 *
 * @author	   Logisim
 * @category   Mej_Doc
 * @package    Mej_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Mejdoc extends Jimw_Db_Table
{
	protected $_name = 'mejdoc';
	
	/**
	 * Find all MEJ documents by the tree_id field
	 *
	 * @param int $id
	 * @return MejDoc_Row
	 */
	public function findByTree($id) {
		return $this->fetchAll(array('tree_id = ?' => $id));
	}
	
}
?>