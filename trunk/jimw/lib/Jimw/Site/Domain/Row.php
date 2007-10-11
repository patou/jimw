<?php
/**
 * Jimw_Site_Domain_Row
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Domain_Row extends Jimw_Db_Row {

	/**
	 * Display the Url
	 * @param $alias The Alias Name
	 */
	public function toUrl($alias) {
		$link = '';
		$link .= $this->protocol ? $this->protocol : 'http';
		$link .= '://';
		if (!empty($this->subdomain))
			$link .= $this->subdomain . '.';
		$link .= $this->name . '/';
		if (!empty($this->path))
			$link .= $this->path . '/';
		return $link . $alias;
	}

	/**
	 * Display the domain name
	 *
	 * @return unknown
	 */
	public function __toString () {
		return $this->toUrl('');
	}
}