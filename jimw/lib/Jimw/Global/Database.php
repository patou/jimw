<?php
/**
 * Jimw_Global_Database
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Global_Database extends Jimw_Db_Table {
	protected $_name = 'database';

	/**
	 * Use the global database
	 *
	 * @param unknown_type $config
	 */
	public function __construct($config = array()) {
		$config[self::ADAPTER] = 'db_global';
		if (empty($config[self::PREFIX])) {
			$config[self::PREFIX] = 'jimw';
		}
		parent::__construct($config);
	}

}