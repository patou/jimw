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
    protected $_paramsField = array();
    protected $_paramsFieldLoaded = array();

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        if (empty($this->_paramsField))
        {
            $table = $this->getTable();
            if (isset($table))
                $this->_paramsField = $table->getParamsField();
        }
    }

    protected function _loadParam ($name = 'param')
    {
        if (!isset($this->_paramsFieldLoaded[$name]) || $this->_paramsFieldLoaded[$name] === null) {
            $value = parent::__get($name);
            if (empty($value))
                $value = serialize(array());
            $this->_paramsFieldLoaded[$name] = unserialize($value);
            if (empty($this->_paramsFieldLoaded[$name])) {
                $this->_paramsFieldLoaded[$name] = array();
            }
        }
    }
    /**
     * Unserialize the tree_param field and save it in the $_param var.
     *
     */
    protected function _saveParam ($name = 'param')
    {
        if (isset($this->_paramsFieldLoaded[$name]) && $this->_paramsFieldLoaded[$name] !== null) {
            $this->__set($name, serialize($this->_paramsFieldLoaded[$name]));
        }
    }
    /**
     * Get a specific value of the param table
     * If the value didn't exist the return value is the boolean false
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function getParam ($name, $default = false, $param = 'param')
    {
        $this->_loadParam($param);
        if (isset($this->_paramsFieldLoaded[$param][$name])) {
            return $this->_paramsFieldLoaded[$param][$name];
        } else {
            return $default;
        }
    }
    /**
     * Get the complet param field
     *
     * @return array
     */
    public function getParams ($param = 'param')
    {
        $this->_loadParam($param);
        return $this->_paramsFieldLoaded[$param];
    }
    /**
     * Check if the param exist
     *
     * @return boolean
     */
    public function issetParam ($name, $param = 'param')
    {
        $this->_loadParam($param);
        return isset($this->_paramsFieldLoaded[$param][$name]);
    }
    /**
     * Set a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function setParam ($name, $value, $param = 'param')
    {
        $this->_loadParam($param);
        $this->_paramsFieldLoaded[$param][$name] = $value;
        $this->_saveParam($param);
    }
    /**
     * Unset a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function unsetParam ($name, $param = 'param')
    {
        $this->_loadParam($param);
        unset($this->_paramsFieldLoaded[$param][$name]);
        $this->_saveParam($param);
    }
    /**
     * Set the complet param field by merging with the last param field
     *
     * @return array
     */
    public function setParams (array $values, $param = 'param')
    {
        $this->_loadParam($param);
        $this->_paramsFieldLoaded[$param] = array_merge($this->_paramsFieldLoaded[$param], $values);
        $this->_saveParam($param);
    }
    /**
     * If the name is 'param', return the Tree param class
     *
     * @param string $name
     * @return mixed
     */
    public function __get ($name)
    {
        if (in_array($name, $this->_paramsField)) {
            return new Jimw_Db_Row_Param($this, $name);
        } else
            return parent::__get($name);
    }

    public function __set ($name, $value)
    {
        if (in_array($name, $this->_paramsField)) {
            if (is_array($value))
                return $this->setParams($value, $name);
        }
        if ($value instanceof Zend_Date) {
        	$value = $this->getFormatedDate($value);
        }
        parent::__set($name, $value);
    }
    
    public function __isset($name) {
    	return (in_array($name, $this->_paramsField) || parent::__isset($name));
    }

    public function __sleep ()
    {
        foreach ($this->_paramsField as $name)
        {
            $this->_saveParam($name);
        }
        return parent::__sleep();
    }

    public function __wakeup ()
    {
        parent::__wakeup();
        foreach ($this->_paramsField as $name)
        {
            $this->_loadParam($name);
        }
    }

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($this->_paramsField as $name)
        {
            $tname = $this->_transformColumn($name);
            if (isset($array[$tname])) {
                $array[$tname] = $this->getParams($name);
            }
        }
        return $array;
    }

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
    
	public function getFormatedDate(Zend_Date $date) {
    	switch (get_class($this->getTable()->getAdapter())) {
    		case 'Zend_Db_Adapter_Mysqli':
    		case 'Zend_Db_Adapter_Pdo_Mysql':
    		case 'Zend_Db_Adapter_Pdo_Sqlite':
    			return $date->toString('YYYY-MM-dd HH:mm:ss');
    		default:
    			return $date->getIso();
    	}	
    }
}
?>