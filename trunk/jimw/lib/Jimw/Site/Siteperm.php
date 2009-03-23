<?php
/**
 * Jimw_Site_Siteperm
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Siteperm extends Jimw_Db_Table
{
    protected $_name = 'siteperm';
    protected $_dependentTables = array('Jimw_Site_Group', 'Jimw_Site_Site');
    protected $_referenceMap = array(
    	'site' => array(
    		'columns' => array('site_id') ,
    		'refTableClass' => 'Jimw_Site_Site' ,
    		'refColumns' => 'site_id') ,
    	'group' => array(
    		'columns' => array('group_id') ,
    		'refTableClass' => 'Jimw_Site_Group' ,
    		'refColumns' => 'group_id'));
    protected $_paramsField = array('perm');
}