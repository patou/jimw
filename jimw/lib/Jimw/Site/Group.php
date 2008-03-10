<?php
/**
 * Jimw_Site_Group
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Group extends Jimw_Db_Table {
	protected $_name = 'group';
	protected $_dependentTables = array('Jimw_Site_UserGroup', 'Jimw_Site_User');
}