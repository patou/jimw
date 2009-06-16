<?php
/**
 * Jimw_Site_Tree_Row
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Tree_Row extends Jimw_Db_Row implements RecursiveIterator, Countable 
{
    protected $_paramsField = array('param');
    public function __construct (array $config = array ())
    {
        //if (!is_array($config))
        //throw new Exception(Jimw_Debug::dump($config, '', false));
        if (is_array($config['data']) && isset($config['data']['active'])) {
            $this->_active = true;
            unset($config['data']['active']);
        }
        if (is_array($config['data']) && isset($config['data']['expanded'])) {
            $this->_expanded = true;
            unset($config['data']['expanded']);
        }
        parent::__construct($config);
    }
    /**
     * Set if the current Row is the current page
     *
     * @var boolean
     */
    protected $_active = false;
    public function getActive ()
    {
        return $this->_active;
    }
    public function setActive ($active)
    {
        $this->_active = $active;
    }
    /**
     * Set if the current Page is the curent page or his children
     *
     * @var boolean
     */
    protected $_expanded = false;
    public function getExpanded ()
    {
        return $this->_expanded;
    }
    public function setExpanded ($expanded)
    {
        $this->_expanded = $expanded;
    }
    public function isVisible ()
    {
        return $this->status == 4;
    }
    
    //
    // RecursiveIterator implementation
    //
    
    protected $_children = null;
    
    /**
     * Returns current page
     *
     * Implements RecursiveIterator interface.
     *
     * @return Zend_Navigation_Page       current page or null
     * @throws Zend_Navigation_Exception  if the index is invalid
     */
    public function current()
    {
        $this->getChildren();
        return current($this->_index);
    }

    /**
     * Returns hash code of current page
     *
     * Implements RecursiveIterator interface.
     *
     * @return string  hash code of current page
     */
    public function key()
    {
        $this->getChildren();
        return key($this->_children);
    }

    /**
     * Moves index pointer to next page in the container
     *
     * Implements RecursiveIterator interface.
     *
     * @return void
     */
    public function next()
    {
        $this->getChildren();
        next($this->_children);
    }

    /**
     * Sets index pointer to first page in the container
     *
     * Implements RecursiveIterator interface.
     *
     * @return void
     */
    public function rewind()
    {
        $this->getChildren();
        reset($this->_children);
    }

    /**
     * Checks if container index is valid
     *
     * Implements RecursiveIterator interface.
     *
     * @return bool
     */
    public function valid()
    {
        $this->getChildren();
        return current($this->_children) !== false;
    }
    
    /**
     * Return the children tree node list
     *
     * Implements RecursiveIterator interface.
     *
     * @return Jimw_Db_Rowset
     */
    public function getChildren ()
    {
    	if ($this->_children == null) {
        	$this->_children = $this->_table->getChildren($this->id);
    	}
    	return $this->_children;
    }
    /**
     * Return if the tree node has children
     *
     * Implements RecursiveIterator interface.
     *
     * @return boolean
     */
    public function hasChildren ()
    {
        //return $this->_table->hasChildren ($this->id);
        return $this->rgt - $this->lft > 1;
    }
    
    // Countable interface:

    /**
     * Returns number of pages in container
     *
     * Implements Countable interface.
     *
     * @return int  number of children in the tree
     */
    public function count()
    {
        return count($this->_children);
    }
    
    /**
     * Return the parent of the Tree node
     *
     * @return Jimw_Site_Tree_Row
     */
    public function getParent ()
    {
        return $this->_table->find($this->parentid);
    }
    public function _postInsert ()
    {
        /*$parent = $this->_data['tree_parentid'];
        if ($parent != 0) {
            $this->_data['tree_parentid'] = 0;
            $this->_table->move_to($this, $parent, Jimw_Site_Tree::CHILD);
            $this->_data['tree_parentid'] = $parent;
        }*/
    }
    public function _update ()
    {
        if ($this->_data['tree_parentid'] != $this->_cleanData['tree_parentid']) {
            $parent = $this->_data['tree_parentid'];
            $this->_data['tree_parentid'] = $this->_cleanData['tree_parentid'];
            $this->_table->move_to($this, $parent, Jimw_Site_Tree::CHILD);
            $this->_data['tree_parentid'] = $parent;
        }
    }
    public function delete ($type = null)
    {
        $rgt = $this->rgt;
        $lft = $this->lft;
        if ($type !== null) {
            //move all child to $type parent
            $parent = ($type === - 1) ? $this->parentid : $type;
            foreach ($this->getChildren() as $child) {
                $this->_table->move_to($this, $parent, Jimw_Site_Tree::CHILD);
            }
            $this->_refresh();
        } else {
            // Delete all children
            $db = $this->_getTable()->getAdapter();
            $info = $this->_getTable()->info();
            $metadata = $info[Zend_Db_Table_Abstract::METADATA];
            $this->_table->delete(array($db->quoteInto($db->quoteIdentifier('tree_lft', true) . ' > ?', $lft, $metadata['tree_lft']['DATA_TYPE']) , $db->quoteInto($db->quoteIdentifier('tree_rgt', true) . ' < ?', $rgt, $metadata['tree_rgt']['DATA_TYPE'])));
        }
        $diff = $rgt - $lft + 1;
        parent::delete();
        $this->_table->update_old(array('tree_lft' => new Zend_Db_Expr('CASE WHEN tree_lft >= ' . $rgt . ' THEN tree_lft - ' . $diff . ' ELSE tree_lft END') , 'tree_rgt' => new Zend_Db_Expr('CASE WHEN tree_rgt >= ' . $rgt . ' THEN tree_rgt - ' . $diff . ' ELSE tree_rgt END')), null);
    }
}
?>