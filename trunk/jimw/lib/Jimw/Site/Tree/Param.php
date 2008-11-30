<?php
/**
 * Jimw_Site_Tree
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
/**
 * This class permit to use $tree->param->name access to tree params instead of $tree->getParam($name);
 *
 */
class Jimw_Site_Tree_Param implements ArrayAccess
{
    private $_tree;
    /**
     * 
     * @param Jimw_Site_Tree_Row $tree The parent Tree Row
     */
    function __construct (Jimw_Site_Tree_Row $tree)
    {
        $this->_tree = $tree;
    }
    /**
     * @see ArrayAccess::offsetExists()
     *
     * @param offset $offset
     */
    public function offsetExists ($offset)
    {
        return $this->_tree->issetParam($offset);
    }
    /**
     * @see ArrayAccess::offsetGet()
     *
     * @param offset $offset
     */
    public function offsetGet ($offset)
    {
        return $this->_tree->getParam($offset);
    }
    /**
     * @see ArrayAccess::offsetSet()
     *
     * @param offset $offset
     * @param value $value
     */
    public function offsetSet ($offset, $value)
    {
        return $this->_tree->setParam($offset, $value);
    }
    /**
     * @see ArrayAccess::offsetUnset()
     *
     * @param offset $offset
     */
    public function offsetUnset ($offset)
    {
        return $this->_tree->unsetParam($offset);
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
        return $this->_tree->getParam($name, $default);
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
        return $this->_tree->getParam($name);
    }
    /**
     * Set a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set ($name, $value)
    {
        return $this->_tree->setParam($name, $value);
    }
    /**
     * Set a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function set ($name, $value)
    {
        return $this->_tree->setParam($name, $value);
    }
    /**
     * Unset a specific value of the param table
     *
     * @param string $name
     * @param mixed $value
     */
    public function __unset ($name)
    {
        return $this->_tree->unsetParam($name);
    }
    /**
     * Check if a specific value exist
     *
     * @param string $name
     * @param mixed $value
     */
    public function __isset ($name)
    {
        return $this->_tree->issetParam($name);
    }
    /**
     * Get the complet param field
     *
     * @return array
     */
    public function toArray ()
    {
        return $this->_tree->getParams();
    }
}

