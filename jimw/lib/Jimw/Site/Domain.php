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
    protected function _getCacheList ()
    {
        if (self::$_listCache === null) {
            $list = $this->fetchAll();
            foreach ($list as $item) {
                self::$_listCache[$item->id] = $item;
            }
        }
    }
    /**
     * Find with a cache
     *
     * @param  mixed $key The value(s) of the primary keys.
     * @return Jimw_Db_Rowset Row(s) matching the criteria.
     * @throws Zend_Db_Table_Exception
     */
    public function findCache ()
    {
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
    /**
     * Fetches all rows from cache
     *
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAllCache ()
    {
        $this->_getCacheList();
        $list = array();
        foreach (self::$_listCache as $item) {
            $list[] = $item->toArray();
        }
        $data = array('table' => $this , 'data' => $list , 'rowClass' => $this->_rowClass , 'stored' => true);
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
    }
    /**
     * Clear the cache
     *
     */
    public function clearCache ()
    {
        //unset($this->_listCache);
        self::$_listCache = null;
    }
    /**
     * create a new Jimw_Site_Row from an url
     *
     * @param String $url
     * @return Jimw_Site_Domain_Row
     */
    public function createFromUrl (String $url)
    {
        $domain = $this->fetchNew();
        $url = Zend_Uri::factory($url);
        $host = $url->getHost();
        $tab_name = explode('.', $host);
        if (count($tab_name) >= 3) {
            $domain->subdomain = $tab_name[0];
            $domain->name = implode('.', array_slice($tab_name, 1));
        } else {
            $domain->subdomain = '';
            $domain->name = $host;
        }
        $domain->port = is_int($port = $url->getPort()) ? $port : 80;
        $domain->protocol = $url->getScheme();
        $domain->path = trim($url->getPath(), '/');
        return $domain;
    }
}