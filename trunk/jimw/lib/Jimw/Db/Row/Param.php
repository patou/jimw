<?php
/**
 * Jimw_Site_Tree
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Param.php 253 2008-11-30 22:34:54Z patou.de.saint.steban $
 */
/**
 * This class permit to use $tree->param->name access to tree params instead of $tree->getParam($name);
 *
 */
class Jimw_Db_Row_Param implements ArrayAccess
{
    private $_row;
    private $_param;
    /**
     *
     * @param Jimw_Site_Tree_Row $tree The parent Tree Row
     */
    function __construct (Jimw_Db_Row $row, $param = 'param')
    {
        $this->_row = $row;
        $this->_param = $param;
    }
    /**
     * @see ArrayAccess::offsetExists()
     *
     * @param offset $offset
     */
    public function offsetExists ($offset)
    {
        return $this->_row->issetParam($offset, $this->_param);
    }
    /**
     * @see ArrayAccess::offsetGet()
     *
     * @param offset $offset
     */
    public function offsetGet ($offset)
    {
        return $this->_row->getParam($offset, $this->_param);
    }
    /**
     * @see ArrayAccess::offsetSet()
     *
     * @param offset $offset
     * @param value $value
     */
    public function offsetSet ($offset, $value)
    {
        return $this->_row->setParam($offset, $value, $this->_param);
    }
    /**
     * @see ArrayAccess::offsetUnset()
     *
     * @param offset $offset
     */
    public function offsetUnset ($offset)
    {
        return $this->_row->unsetParam($offset, $this->_param);
    }
    /**
     * Get a specific value of the param table
     * If the value didn't exist the default value is returned
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function get ($name, $default = false)
    {
        return $this->_row->getParam($name, $default, $this->_param);
    }
    /**
     * Get a specific value of the param table
     * If the value didn't exist the return value is the boolean false
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function __get ($name)
    {
        return $this->_row->getParam($name, $this->_param);
    }
    /**
     * Set a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set ($name, $value)
    {
        return $this->_row->setParam($name, $value, $this->_param);
    }
    /**
     * Set a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function set ($name, $value)
    {
        return $this->_row->setParam($name, $value, $this->_param);
    }
    /**
     * Unset a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function __unset ($name)
    {
        return $this->_row->unsetParam($name, $this->_param);
    }
    /**
     * Check if a specific value exist
     *
     * @param string $name
     * @param mixed $value
     */
    public function __isset ($name)
    {
        return $this->_row->issetParam($name, $this->_param);
    }
    /**
     * Get the complet param field
     *
     * @return array
     */
    public function toArray ()
    {
        return $this->_row->getParams($this->_param);
    }
}

