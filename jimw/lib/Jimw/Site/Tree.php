<?php
/**
 * Jimw_Site_Tree
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Tree extends Jimw_Db_Table
{
    /*
     * Status constants
     */
    const UNPUBLISHED = 0;
    const DRAFT = 1;
    const CORRECTING = 2;
    const TOBEPUBLISHED = 3;
    const PUBLISHED = 4;
    const ARCHIVED = 5;
    const DELETED = - 1;
    
    public static $STATUS_LIST = array(self::UNPUBLISHED => 'Unpublished' , self::DRAFT => 'Draft' , self::CORRECTING => 'Correcting' , self::TOBEPUBLISHED => 'To be published' , self::PUBLISHED => 'Published' , self::ARCHIVED => 'Archived' , self::DELETED => 'Deleted');
    /*
     * Child contants
     */
    const CHILD = 0;
    const LEFT = 1;
    const RIGHT = 2;
    // Table Name
    protected $_name = 'tree';
    // Interne representation
    protected $_rowClass = 'Jimw_Site_Tree_Row';
    protected $_rowsetClass = 'Jimw_Site_Tree_Rowset';
    protected $_dependentTables = array('Jimw_Site_Site' , 'Jimw_Site_Module');
    protected $_referenceMap = array('site' => array('columns' => array('site_id') , 'refTableClass' => 'Jimw_Site_Site' , 'refColumns' => 'site_id') , 'module' => array('columns' => array('module_path') , 'refTableClass' => 'Jimw_Site_Module' , 'refColumns' => 'module_path'));
    // Cache variable
    protected static $_children = null;
    protected static $_nodes = null;
    protected static $_nodesAlias = null;
    /**
     * Current tree page
     * @var unknown_type
     */
    protected static $_currentid = - 1;
    /**
     * Set the current page tree
     *
     * @param int $currentid
     */
    public static function setCurrent ($currentid)
    {
        self::$_currentid = $currentid;
        self::_clearTree();
    }
    /**
     * Get the current page tree
     *
     * @return int
     */
    public static function getCurrent ()
    {
        return self::$_currentid;
    }
    /**
     * Load the all tree into the class and keep it in the class
     *
     */
    private function _loadTree ()
    {
        if (self::$_children === null || self::$_nodes === null || self::$_nodesAlias === null) {
            $select = $this->select()->order(array('tree_lft' , 'tree_alias'))->where('tree_status >= 0');
            $result = $this->_fetch($select);
            //ZF1.0 : $result = $this->_fetch(null, array('tree_lft', 'tree_alias'));
            if ($result === false) {
                return;
            } else {
                foreach ($result as $node) {
                    self::$_nodes[$node['tree_id']] = $node;
                    self::$_nodesAlias[$node['tree_alias']] = $node['tree_id'];
                    self::$_children[$node['tree_parentid']][] = $node['tree_id'];
                }
                if (self::$_currentid >= 0) {
                    $this->_setExpandTree();
                }
            }
        }
    }
    /**
     * Clear the tree cache
     * @return unknown_type
     */
    private static function _clearTree ()
    {
        //unset(self::$_nodes);
        self::$_nodes = null;
        //unset(self::$_nodesAlias);
        self::$_nodesAlias = null;
        //unset(self::$_children);
        self::$_children = null;
    }
    /**
     * Automatically expand the tree (get the current tree and expand the parent branch)
     */
    private function _setExpandTree ()
    {
        $newid = self::$_nodes[self::$_currentid]['tree_parentid'];
        self::$_nodes[self::$_currentid]['expanded'] = true;
        self::$_nodes[self::$_currentid]['active'] = true;
        while (isset(self::$_nodes[$newid])) {
            self::$_nodes[$newid]['expanded'] = true;
            $newid = self::$_nodes[$newid]['tree_parentid'];
        }
    }
    /**
     * Get all children of a parent givent tree
     * @param $parent the parent tree id
     * @return Jimw_Site_Tree_Rowset the list of children
     */
    public function getChildren ($parent = 0)
    {
        $this->_loadTree();
        $list = array();
        if (isset(self::$_children[$parent])) {
            foreach (self::$_children[$parent] as $child) {
                $list[] = self::$_nodes[$child];
            }
        }
        $data = array('table' => $this , 'data' => $list , 'stored' => true , 'rowClass' => $this->_rowClass);
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
        //return $list;
    }
    /**
     * Test if the Tree id given has children
     *
     * @param int $parent
     * @return boolean
     */
    public function hasChildren ($parent = 0)
    {
        $this->_loadTree();
        return (isset(self::$_children[$parent]));
    }
    /**
     * Create the alias name if not exist
     *
     * @param string $sString
     * @return string
     */
    private function getRewriteString ($sString)
    {
        //$string = strtolower( htmlentities( strtr($sString, array("?"=>"Ae", "?"=>"Ue", "?"=>"Oe", "?"=>"ae", "?"=>"ue", "?"=>"oe")) ) );
        $string = strtolower(htmlentities($sString));
        $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml|grave);/", "$1", $string);
        $string = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($string));
        $string = trim($string, "-");
        return $string;
    }
    /**
     * Find a Tree item or a list of tree item
     *
     * @param int|array $id
     * @return Jimw_Db_Rowset
     */
    public function find ()
    {
        $args = func_get_args();
        $this->_loadTree();
        $list = array();
        foreach ($args as $i) {
            if (isset(self::$_nodes[$i])) {
                $list[] = self::$_nodes[$i];
            }
        }
        $data = array('table' => $this , 'data' => $list , 'rowClass' => $this->_rowClass , 'stored' => true);
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
    }
    /**
     * Find a Tree Item by alias by using the cache
     *
     * @param string $alias
     * @return boolean|Jimw_Site_Tree_Rowset
     */
    public function findAlias ($alias)
    {
        $id = self::$_nodesAlias[$alias];
        if ($id !== false) {
            $node = isset(self::$_nodes[$id]) ? array(self::$_nodes[$id]) : array();
            $data = array('table' => $this , 'data' => $node , 'rowClass' => $this->_rowClass , 'stored' => true);
            Zend_Loader::loadClass($this->_rowsetClass);
            return new $this->_rowsetClass($data);
        }
        //return $this->fetchAll(array('tree_alias = ?' => $alias));
        return false;
    }
    /**
     * Find a Tree Item by the alias, and get the module and Site
     *
     * @param string $alias
     * @return boolean|Jimw_Site_Tree_Row
     */
    public function findByAliasWithModuleAndSite ($alias)
    {
        $select = $this->select()->where('tree_alias = ?', $alias)->where('tree_status = ?', 4)->order(array('tree_version DESC'));
        $list = $this->_fetch($select);
        if ($list) {
            $list = $list[0];
            $module = new Jimw_Site_Module();
            $list['module'] = $module->find($list['module_path'])->current();
            $site = new Jimw_Site_Site();
            $list['site'] = $site->find($list['site_id'])->current();
            $data = array('table' => $this , 'data' => $list , 'rowClass' => $this->_rowClass , 'stored' => true , 'readOnly' => true);
            Zend_Loader::loadClass($this->_rowClass);
            return new $this->_rowClass($data);
        }
        return false;
    }
    /**
     * Find a Tree Item by the , and get the module and Site
     *
     * @param string $alias
     * @return boolean|Jimw_Site_Tree_Row
     */
    public function findBySite ($site)
    {
        $this->_loadTree();
        $id = $site->default_tree_id;
        if (isset(self::$_nodes[$id])) {
            $list = self::$_nodes[$id];
            $module = new Jimw_Site_Module();
            $list['module'] = $module->find($list['module_path'])->current();
            $list['site'] = $site;
            $data = array('table' => $this , 'data' => $list , 'rowClass' => $this->_rowClass , 'stored' => true , 'readOnly' => true);
            Zend_Loader::loadClass($this->_rowClass);
            return new $this->_rowClass($data);
        }
        return false;
    }
    /**
     * Get the date of the most recently modified page
     *
     * @return boolean|date
     */
    public function getLastModified ()
    {
        $this->_loadTree();
        $lastdate = false;
        foreach (self::$_nodes as $k => $node) {
            if ($node['tree_editiondate'] > $lastdate)
                $lastdate = $node['tree_editiondate'];
        }
        return $lastdate;
    }
    /**
     * (non-PHPdoc)
     * @see jimw/lib/Jimw/Db/Jimw_Db_Table#insert($data)
     */
    public function insert (array $data)
    {
        // add a timestamp
        if (empty($data['tree_pagetitle']) && ! empty($data['tree_menutitle'])) {
            $data['tree_pagetitle'] = $data['tree_menutitle'];
        } elseif (empty($data['tree_menutitle']) && ! empty($data['tree_pagetitle'])) {
            $data['tree_menutitle'] = $data['tree_pagetitle'];
        }
        if (empty($data['tree_alias'])) {
            $data['tree_alias'] = $this->getRewriteString($data['tree_menutitle']);
        }
        if (empty($data['tree_status'])) {
            $data['tree_status'] = self::PUBLISHED;
        }
        if (empty($data['tree_type'])) {
            $data['tree_type'] = 0;
        }
        if (empty($data['tree_version'])) {
            $data['tree_version'] = 0;
        }
        if (empty($data['tree_creationdate'])) {
            $data['tree_creationdate'] = $this->getFormatedDate(Zend_Date::now()); //new Zend_Db_Expr('NOW()');
        }
        if (empty($data['tree_editiondate'])) {
            $data['tree_editiondate'] = $this->getFormatedDate(Zend_Date::now()); //new Zend_Db_Expr('NOW()');;
        }
        /*unset($data['tree_lft']);
		unset($data['tree_rgt']);*/
        $id = parent::insert($data);
        self::_clearTree();
        return $id;
    }
    /**
     * Override the default createRow to add some default values
     *
     * @param array $data
     * @return Jimw_Db_RowClass
     */
    public function createRow (array $data = array(), $defaultSource = null)
    {
        $data['tree_creationdate'] = $this->getFormatedDate(Zend_Date::now());
        $data['tree_editiondate'] = $this->getFormatedDate(Zend_Date::now());
        $data['tree_type'] = 0;
        $data['tree_status'] = self::PUBLISHED;
        $data['tree_param'] = '';
        $select = $this->_db->select();
        $select->from($this->_name, new Zend_Db_Expr('MAX(tree_rgt)'));
        $max = $this->_db->fetchOne($select);
        $data['tree_lft'] = $max + 1;
        $data['tree_rgt'] = $max + 2;
        return parent::createRow($data, $defaultSource);
    }
    /**
     * Return a Zend_Db_Select class but add where clause to select all children of the selected parent tree
     * @param $parent the tree parent id
     * @return Zend_Db_Select
     */
    public function selectChildren ($parent)
    {
        if (! ($parent instanceof Jimw_Site_Tree_Row)) {
            $parent = $this->find($parent)->current();
        }
        if (! $parent) { // Root
            return $this->select()->order('tree_lft');
        } else {
            return $this->select()->where('tree_lft >= ?', $parent->lft)->where('tree_rgt <= ? ', $parent->rgt)->order('tree_lft');
        }
    }
    /**
     * Same as the fetch method but restrict to all children of the parent tree
     * @param $parent the tree parent id
     * @param $where
     * @param $order
     * @param $count
     * @param $offset
     * @return unknown_type
     */
    public function fetchAllChildren ($parent, $where = null, $order = null, $count = null, $offset = null)
    {
        if (! ($parent instanceof Jimw_Site_Tree_Row)) {
            $parent = $this->find($parent)->current();
        }
        if (empty($order)) {
            $order = array('tree_lft');
        }
        if (! $parent) { // Root
            return $this->fetchAll($where, $order, $count);
        } else {
            return $this->fetchAll($this->_buildWhere($parent->lft, $parent->rgt, $where), $order, $count, $offset);
        }
    }
    /**
     * Update all children from a parent tree
     * @param $parent
     * @param $data
     * @param $where
     * @return unknown_type
     */
    public function updateAllChildren ($parent, $data, $where = null)
    {
        if (! ($parent instanceof Jimw_Site_Tree_Row)) {
            $parent = $this->find($parent)->current();
        }
        if (! $parent) {
            return $this->update_old($data, $where);
        } else {
            return $this->update_old($data, $this->_buildWhere($parent->lft, $parent->rgt, $where));
        }
    }
    /**
     * Build a where clause with lft and rgt automatically setted
     * @param $lft
     * @param $rgt
     * @param $where
     * @param $col
     * @return unknown_type
     */
    public function _buildWhere ($lft, $rgt, $where = null, $col = false)
    {
        if ($where instanceof Zend_Db_Select) {
            return $where->where(($col ? $col : 'tree_lft') . ' >= ?', $lft)->where(($col ? $col : 'tree_rgt') . ' <= ?', $rgt);
        }
        $where_array = array($this->_db->quoteInto($this->_db->quoteIdentifier(($col) ? $col : 'tree_lft', true) . ' >= ?', $lft) , $this->_db->quoteInto($this->_db->quoteIdentifier(($col) ? $col : 'tree_rgt', true) . ' <= ?', $rgt));
        if (empty($where)) {
            return $where_array;
        } elseif (is_array($where)) {
            return array_merge($where, $where_array);
        } else {
            $where_array[] = $where;
            return $where_array;
        }
    }
    /**
     * (non-PHPdoc)
     * @see jimw/lib/Zend/Db/Table/Zend_Db_Table_Abstract#delete($where)
     */
    public function delete ($where)
    {
        /*def before_destroy
 	          return if self[acts_as_nested_set_options[:right_column]].nil? || self[acts_as_nested_set_options[:left_column]].nil?
 	          dif = self[acts_as_nested_set_options[:right_column]] - self[acts_as_nested_set_options[:left_column]] + 1

 	          self.class.transaction {
 	            self.class.delete_all( "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} > #{self[acts_as_nested_set_options[:left_column]]} and #{acts_as_nested_set_options[:right_column]} < #{self[acts_as_nested_set_options[:right_column]]}" )
 	            self.class.update_all( "#{acts_as_nested_set_options[:left_column]} = (#{acts_as_nested_set_options[:left_column]} - #{dif})",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} >= #{self[acts_as_nested_set_options[:right_column]]}" )
 	            self.class.update_all( "#{acts_as_nested_set_options[:right_column]} = (#{acts_as_nested_set_options[:right_column]} - #{dif} )",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:right_column]} >= #{self[acts_as_nested_set_options[:right_column]]}" )
 	          }
 	        end*/
        $id = parent::delete($where);
        self::_clearTree();
        return $id;
    }
    /**
     * (non-PHPdoc)
     * @see jimw/lib/Jimw/Db/Jimw_Db_Table#update($data, $where)
     */
    public function update (array $data, $where)
    {
        // add a timestamp
        if (empty($data['tree_editiondate'])) {
            $data['tree_editiondate'] = $this->getFormatedDate(Zend_Date::now()); //new Zend_Db_Expr('NOW()');
        }
        if (!empty($data['tree_version'])) {
            $data['tree_version']++;
        } else {
            $data['tree_version']=1;
        }
        unset($data['tree_lft']);
        unset($data['tree_rgt']);
        $id = parent::update($data, $where);
        self::_clearTree();
        return $id;
    }
    /**
     * 
     * @param $data
     * @param $where
     * @return unknown_type
     */
    public function update_old (array $data, $where)
    {
        return parent::update($data, $where);
    }

    /**
     * Move a tree to another location
     * @param Jimw_Site_Tree_Row $tree The tree to move
     * @param $target The target to atach the moved tree Can be a Jimw_Site_Tree_Row or the id
     * @param $position Position of the moved tree with the target
     * @return unknown_type
     */
    public function move_to (Jimw_Site_Tree_Row $tree, $target, $position)
    {
        $cur_left = $tree->lft;
        $cur_right = $tree->rgt;
        $extent = $cur_right - $cur_left + 1;
        if (! ($target instanceof Jimw_Site_Tree_Row) && $target != 0) {
            $target = $this->find($target)->current();
        }
        if (! $target) { //Root
            //throw new Jimw_Exception('Not found');
            if ($position == self::RIGHT) {
                //Move on LEFT of last Root
                $target = $this->fetchRow(array('tree_parentid = ?' => 0), 'tree_lft ASC');
            } else {
                //Move on RIGHT of first Root
                $target = $this->fetchRow(array('tree_parentid = ?' => 0), 'tree_rgt DESC');
                $position = self::LEFT;
            }
            if (! $target)
                throw new Jimw_Exception('Target not found');
        }
        $target_left = $target->lft;
        $target_right = $target->rgt;
        if (($target_left >= $cur_left) && ($target_right <= $cur_right)) {
            throw new Jimw_Exception('Ilegal tree move');
        }
        $tree->parentid = $target->parentid;
        switch ($position) {
            case self::CHILD:
                $bound = $target_right;
                break;
            case self::LEFT:
                $bound = $target_left;
                break;
            case self::RIGHT:
                $bound = $target_right + 1;
                break;
            default:
                throw new Jimw_Exception('Position should be either child, left or right (' . $position . ' received)');
        }
        if ($bound > $cur_right) {
            $bound -= 1;
            $other_bound = $cur_right + 1;
        } else {
            $other_bound = $cur_left - 1;
        }
        if ($bound == $cur_left || $bound == $cur_right) {
            return;
        }
        $tab = array($cur_left , $cur_right , $bound , $other_bound);
        sort($tab);
        $a = $b = $c = $d = 0;
        list ($a, $b, $c, $d) = $tab;
        // Transaction for all update
        parent::update(array('tree_lft' => new Zend_Db_Expr('CASE WHEN tree_lft BETWEEN ' . $a . ' AND ' . $b . ' THEN tree_lft + ' . ($d - $b) . ' WHEN tree_lft BETWEEN ' . $c . ' AND ' . $d . ' THEN tree_lft + ' . ($a - $c) . ' ELSE tree_lft END') , 'tree_rgt' => new Zend_Db_Expr('CASE WHEN tree_rgt BETWEEN ' . $a . ' AND ' . $b . ' THEN tree_rgt + ' . ($d - $b) . ' WHEN tree_rgt BETWEEN ' . $c . ' AND ' . $d . ' THEN tree_rgt + ' . ($a - $c) . ' ELSE tree_rgt END')), null);
        self::_clearTree();
    }
}