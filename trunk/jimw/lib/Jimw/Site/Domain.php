<?php
/**
 * Jimw_Site_Domain
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Domain extends Jimw_Db_Table
{
    protected $_name = 'domain';
    protected $_rowClass = 'Jimw_Site_Domain_Row';
    protected $_isGlobalDb = true;
    protected $_dependentTables = array('Jimw_Site_Site');
    protected $_referenceMap = array('site' => array('columns' => array('site_id') , 'refTableClass' => 'Jimw_Site_Site' , 'refColumns' => 'site_id'));
    protected $_cacheData = true;
    /**
     * Get the Domain Row from a Site Id
     *
     * @param int $site_id
     * @return Jimw_Site_Domain_Row|boolean
     */
    public function getDomain ($site_id)
    {
        $database = Zend_Registry::get('database')->id;
        $select = $this->select()->where('site_id = ?', $site_id)->where('database_id = ?', $database)->order('domaine_status');
        return $this->fetchRow($select);
    }
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
    /**
     * Static method to get the Domain Url from a specific site
     * Use a cache and load all domain in the first call of the method
     * @deprecated
     * @param int $site_id The Site id
     * @return string
     */
    public static function toUrl ($site_id, $alias = '')
    {
        Jimw_Debug::deprecated('Jimw_Site_Domain::toUrl', 'Jimw_Site_Domain_Row::toUrl()');
        /*if (self::$_cacheDomain === null) {
            $self = new self();
            $database = Zend_Registry::get('database')->id;
            $rowSet = $self->fetchAll(array('domain_status = ?' => 1 , 'database_id = ?' => $database));
            $dom = array();
            foreach ($rowSet as $row) {
                $dom[$row->site_id] = $row->__toString();
            }
            self::$_cacheDomain = $dom;
        }
        if (! empty($alias))
            return self::$_cacheDomain[$site_id] . $alias . '/';
        else
            return self::$_cacheDomain[$site_id];*/
    }
}