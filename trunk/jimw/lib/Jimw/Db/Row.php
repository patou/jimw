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
class Jimw_Db_Row extends Zend_Db_Table_Row
{
    /**
     * Transform a column name from the user-specified form
     * to the physical form used in the database.
     * You can override this method in a custom Row class
     * to implement column name mappings, for example inflection.
     *
     * @param string $columnName Column name given.
     * @return string The column name after transformation applied (none by default).
     * @throws Zend_Db_Table_Row_Exception if the $columnName is not a string.
     */
    protected function _transformColumn ($columnName)
    {
        if (! is_string($columnName)) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('Specified column is not a string');
        }
        if (! strpos($columnName, '_')) {
            $table = $this->_table;
            if ($table && $table instanceof Jimw_Db_Table) {
                $prefix = $table->getTableName();
                if (! empty($prefix)) {
                    $name = $prefix . '_' . $columnName;
                    if (array_key_exists($name, $this->_data))
                        $columnName = $name;
                }
            }
        }
        return $columnName;
    }
}
?>