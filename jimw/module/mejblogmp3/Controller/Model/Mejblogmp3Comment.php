<?php
/**
 * BlogComment
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Mejblogmp3Comment extends Jimw_Db_Table
{
	protected $_name = 'blogmp3comment';
	protected $_dependentTables = array('Mejblogmp3Message');
	protected $_referenceMap    = array(
	'mejblogmp3' => array(
	'columns'           => array('blogmp3message_id'),
	'refTableClass'     => 'Mejblogmp3Message',
	'refColumns'        => 'blogmp3message_id',
	'onDelete' 			=> 'cascade',
	'onUpdate' 			=> 'cascade',
	));
	/**
	 * Find all blog comments by the blogmessage_id field
	 *
	 * @param int $id
	 * @return Jimw_Db_Rowset
	 */
	public function findByBlogMessage($id) {
		return $this->fetchAll(array('blogmp3message_id = ?' => $id));
	}
}
