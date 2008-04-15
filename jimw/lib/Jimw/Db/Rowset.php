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
    public function exists()
    {
        try {
            throw new Jimw_Exception('Zend_Db_Rowset->exists is deprecated');
        }
        catch (Exception $e) {
            Jimw_Debug::display_exception($e);
        }
        return $this->_count > 0;
    }
}
