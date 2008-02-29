<?php
/**
 * Jimw_Site_Site
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Site extends Jimw_Db_Table {
	protected $_name = 'site';
	protected $_dependentTables = array('Jimw_Site_Tree', 'Jimw_Site_Domain', 'Jimw_Site_UserGroup');
	/**protected $_referenceMap    = array(
	'root' => array(
	'columns'           => array('tree_id'),
	'refTableClass'     => 'Jimw_Site_Tree',
	'refColumns'        => 'tree_id'
	));*/
}