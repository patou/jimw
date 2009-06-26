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
    protected $_dependentTables = array('Jimw_Site_Usergroup');
    protected $_paramsField = array('information');
    
    public function insert (array $data)
    {
        if (empty($data['user_creationdate'])) {
            $data['user_creationdate'] = $this->getFormatedDate(Zend_Date::now());
        }
        if (empty($data['user_lastvisit'])) {
            $data['user_lastvisit'] = $this->getFormatedDate(new Zend_Date(0));
        }
        return parent::insert($data);
    }
}