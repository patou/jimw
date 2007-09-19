<?php
/**
 * Jimw_Site_Module
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Module extends Jimw_Db_Table {
	protected $_name = 'module';
	protected $_dependentTables = array('Tree');
}