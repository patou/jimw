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
class Jimw_Site_Tree_Row extends Jimw_Db_Row {
    protected $_paramsField = array('param');
    protected $_module = null;
    protected $_active = false;
    protected $_expanded = false;
    protected $_children = null;
    
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
     * If the current Row is the current page
     * @return boolean
     */
    public function getActive ()
    {
        return $this->_active;
    }
    
    /**
     * Set if the current Row is the current page
     *
     * @var boolean
     */
    public function setActive ($active)
    {
        $this->_active = $active;
    }
    
    /**
     * If the current page is the current page or his children
     * @return boolean 
     */
    public function getExpanded ()
    {
        return $this->_expanded;
    }

    /**
     * Set if the current Page is the curent page or his children
     *
     * @var boolean
     */
    public function setExpanded ($expanded)
    {
        $this->_expanded = $expanded;
    }
    /**
     * Return if this tree is visible
     *
     * @return boolean
     */
    public function isVisible ()
    {
        return $this->status == Jimw_Site_Tree::PUBLISHED;
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
        $this->_getChildren();
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
        return $this->rgt - $this->lft > 1;
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
    /**
     * (non-PHPdoc)
     * @see jimw/lib/Zend/Db/Table/Row/Zend_Db_Table_Row_Abstract#_postInsert()
     */
    public function _postInsert ()
    {    /*$parent = $this->_data['tree_parentid'];
        if ($parent != 0) {
            $this->_data['tree_parentid'] = 0;
            $this->_table->move_to($this, $parent, Jimw_Site_Tree::CHILD);
            $this->_data['tree_parentid'] = $parent;
        }*/
    }
    /**
     * (non-PHPdoc)
     * @see jimw/lib/Zend/Db/Table/Row/Zend_Db_Table_Row_Abstract#_update()
     */
    public function _update ()
    {
        if ($this->_data['tree_parentid'] != $this->_cleanData['tree_parentid']) {
            $parent = $this->_data['tree_parentid'];
            $this->_data['tree_parentid'] = $this->_cleanData['tree_parentid'];
            $this->_table->move_to($this, $parent, Jimw_Site_Tree::CHILD);
            $this->_data['tree_parentid'] = $parent;
        }
    }
    /**
     * (non-PHPdoc)
     * @see jimw/lib/Zend/Db/Table/Row/Zend_Db_Table_Row_Abstract#delete()
     */
    public function delete ($type = null)
    {
        $rgt = $this->rgt;
        $lft = $this->lft;
        if ($type !== null) {
            //move all child to $type parent
            $parent = ($type === - 1) ? $this->parentid : $type;
            foreach ($this->_getChildren() as $child) {
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
    /**
     * Return the children tree node list
     *
     * Implements RecursiveIterator interface.
     *
     * @return Jimw_Db_Rowset
     */
    private function _getChildren ()
    {
        if ($this->_children == null) {
            $this->_children = $this->_table->getChildren($this->id);
        }
        //Jimw_Debug::dump($this->_children);
        //return $this->_children;
        return null;
    }

    

    /**
     * Return the module Row from the tree, use the module_path to load the module
     * @return Jimw_Site_Module_Row The module
     */
    public function getModule() {
        if (!$this->_module) {
            $moduletable = new Jimw_Site_Module();
            $module_path = $this->__get('module_path');
            if ($module_path) {
                $this->_module = $moduletable->fetchRow($moduletable->select()->where('module_path = ?', $module_path));
            }
        }
        return $this->_module;
    }

    /**
     * Return the column
     * If the name is "module", return the associated module
     * @param string $name
     * @return mixed
     * @see Zend_Db_Table_Row_Abstract#__get()
     */
    public function __get($name) {
        if ($name == 'module') {
            return $this->getModule();
        }
        return parent::__get($name);
    }
}
?>