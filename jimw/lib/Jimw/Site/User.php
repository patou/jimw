<?php
/**
 * Jimw_Site_User
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_User extends Jimw_Db_Table
{
    protected $_name = 'user';
    protected $_dependentTables = array('Jimw_Site_UserGroup');
    public function insert (array $data)
    {
        if (empty($data['user_creationdate'])) {
            $data['user_creationdate'] = new Zend_Db_Expr('NOW()');
        }
        if (empty($data['user_lastvisit'])) {
            $data['user_lastvisit'] = '1900-01-01 00:00:00';
        }
        /*unset($data['tree_lft']);
		unset($data['tree_rgt']);*/
        return parent::insert($data);
    }
}