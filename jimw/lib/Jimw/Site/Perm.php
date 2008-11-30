<?php
/**
 * Jimw_Site_Perm
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Perm extends Jimw_Db_Table
{
    protected $_name = 'perm';
    protected $_dependentTables = array('Jimw_Site_UserGroup');
    protected $_referenceMap = array('usergroup' => array('columns' => array('usergroup_id') , 'refTableClass' => 'Jimw_Site_UserGroup' , 'refColumns' => 'usergroup_id'));
}