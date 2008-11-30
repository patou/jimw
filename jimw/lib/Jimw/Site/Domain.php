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
    public static $_cacheDomain = null;
    /**
     * Static method to get the Domain Url from a specific site
     * Use a cache and load all domain in the first call of the method
     * @deprecated
     * @param int $site_id The Site id
     * @return string
     */
    public static function toUrl ($site_id, $alias = '')
    {
        if (self::$_cacheDomain === null) {
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
            return self::$_cacheDomain[$site_id];
    }
}