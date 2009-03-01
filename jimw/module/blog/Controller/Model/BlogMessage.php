<?php
/**
 * BlogMessage
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
include_once (dirname(__FILE__) . '/BlogMessage/Row.php');
class BlogMessage extends Jimw_Db_Table
{
	protected $_name = 'blogmessage';
	protected $_dependentTables = array('BlogComment');
	protected $_rowClass = 'BlogMessage_Row';
}
