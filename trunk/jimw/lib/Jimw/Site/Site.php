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
class Jimw_Site_Site extends Jimw_Db_Table
{
    protected $_name = 'site';
    protected $_dependentTables = array('Jimw_Site_Tree' , 'Jimw_Site_Domain' , 'Jimw_Site_Usergroup');
    protected $_referenceMap = array('root' => array('columns' => array('tree_id') , 'refTableClass' => 'Jimw_Site_Tree' , 'refColumns' => 'tree_id') , 'default_page' => array('columns' => array('default_tree_id') , 'refTableClass' => 'Jimw_Site_Tree' , 'refColumns' => 'tree_id') , 'domain' => array('columns' => array('domain_id') , 'refTableClass' => 'Jimw_Site_Domain' , 'refColumns' => 'domain_id'));
    protected static $_listCache = null;

    protected function _getCacheList() {
        if (self::$_listCache === null) {
            $list = $this->fetchAll();
            foreach ($list as $item) {
                self::$_listCache[$item->id] =  $item;
            }
        }
    }

    public function findCache() {
        $args = func_get_args();
        $this->_getCacheList();
        $list = array();
        foreach ($args as $i) {
            if (isset(self::$_listCache[$i])) {
                $list[] = self::$_listCache[$i]->toArray();
            }
        }
        $data = array('table' => $this , 'data' => $list , 'rowClass' => $this->_rowClass , 'stored' => true);
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
    }

    public function fetchAllCache() {
        $this->_getCacheList();
        $list = array();
        foreach (self::$_listCache as $item) {
            $list[] = $item->toArray();
        }
        $data = array('table' => $this , 'data' => $list , 'rowClass' => $this->_rowClass , 'stored' => true);
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
    }

    public function clearCache() {
        //unset($this->_listCache);
        self::$_listCache = null;
    }

}