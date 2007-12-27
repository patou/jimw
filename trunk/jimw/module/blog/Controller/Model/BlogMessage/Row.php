<?php
/**
 * BlogMessage_Row
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class BlogMessage_Row extends Jimw_Db_Row
{
	protected $comments;

	/**
	 * Set the comments rowset
	 *
	 * @param int $com
	 * @return Jimw_Db_Rowset
	 */
	public function setComments($com) {
		$this->comments = $com;
	}
	
	public function getComments() {
		return $this->comments;
	}
}
?>