<?php
/**
 * Jimw_Site_Usergroup
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Usergroup.php 272 2009-03-23 20:03:42Z patou.de.saint.steban $
 */
class Jimw_Site_Usergroup extends Jimw_Db_Table
{
    protected $_name = 'usergroup';
    protected $_dependentTables = array('Jimw_Site_Group' , 'Jimw_Site_User');
    protected $_referenceMap = array(
    	'user' => array(
    		'columns' => array('user_id') ,
    		'refTableClass' => 'Jimw_Site_User' ,
    		'refColumns' => 'user_id') ,
    	'group' => array(
    		'columns' => array('group_id') ,
    		'refTableClass' => 'Jimw_Site_Group' ,
    		'refColumns' => 'group_id')
    );
}