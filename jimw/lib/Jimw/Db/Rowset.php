<?php
/**
 * Jimw_Db_Rowset
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Db_Rowset extends Zend_Db_Table_Rowset
{
    /**
     * Returns true if and only if count($this) > 0.
     *
     * @return bool
     * @deprecated since 0.9.3; use count() instead
     */
    public function exists ()
    {
        Jimw_Debug::deprecated('Zend_Db_Rowset->exists()', 'cout($row)');
        return $this->_count > 0;
    }
}
