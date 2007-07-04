<?php
/**
 * Jimw_Db_Table
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Db_Table extends Zend_Db_Table 
{
	protected $_rowClass = 'Jimw_Db_Row';
	protected $_rowsetClass = 'Jimw_Db_Rowset';

	protected function _setupTableName()
    {
    	$prefix = Zend_Registry::get('db_prefix');
    	parent::_setupTableName();
        if (!strpos($this->_name, $prefix)) {
        	if (!empty($prefix))
        		$this->_name = $prefix . '_' . $this->_name;
        }
    }
}
?>