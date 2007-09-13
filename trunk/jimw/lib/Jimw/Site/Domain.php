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
class Jimw_Site_Domain extends Jimw_Db_Table {
	protected $_name = 'domain';
	protected $_rowClass = 'Jimw_Site_Domain_Row';

	/**
	 * Get the Domain Row from a Site Id
	 *
	 * @param int $site_id
	 * @return Jimw_Site_Domain_Row|boolean
	 */
	public function getDomain($site_id) {
		$where = $this->getAdapter()->quoteInto('site_id = ?', $site_id);
		return $this->fetchRow($where);
	}

	public static $_cacheDomain = null;
	/**
	 * Static method to get the Domain Url from a specific site
	 * Use a cache and load all domain in the first call of the method
	 * @param int $site_id The Site id
	 * @return string
	 */
	public static function toUrl ($site_id, $alias = '') {
		if (self::$_cacheDomain === null) {
			$self = new self ();
			$rowSet = $self->fetchAll();
			$dom = array();
			foreach ($rowSet as $row) {
				$dom[$row->site_id] = $row->__toString();
			}
			self::$_cacheDomain = $dom;
		}
		if (!empty($alias))
			return self::$_cacheDomain[$site_id] . $alias . '/';
		else
			return self::$_cacheDomain[$site_id];
	}
}