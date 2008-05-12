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
class Jimw_Site_Tree extends Jimw_Db_Table {
	protected $_name = 'tree';
	protected $_rowClass = 'Jimw_Site_Tree_Row';

	protected $_dependentTables = array('Jimw_Site_Site', 'Jimw_Site_Module');
	protected $_referenceMap    = array(
	'site' => array(
	'columns'           => array('site_id'),
	'refTableClass'     => 'Jimw_Site_Site',
	'refColumns'        => 'site_id'
	),
	'module' => array(
	'columns'           => array('module_id'),
	'refTableClass'     => 'Jimw_Site_Module',
	'refColumns'        => 'module_id'
	));

	protected static $_children = null;
	protected static $_nodes = null;
	protected static $_nodesAlias = null;
	protected static $_currentid = -1;

	/**
	 * Set the current page tree
	 *
	 * @param int $currentid
	 */
	public static function setCurrent($currentid) {
		self::$_currentid = $currentid;
	}


	/**
	 * Get the current page tree
	 *
	 * @return int
	 */
	public static function getCurrent() {
		return self::$_currentid;
	}

	/**
	 * Load the all tree into the class and keep it in the class
	 *
	 */
	private function _loadTree () {
		if (self::$_children === null || self::$_nodes === null || self::$_nodesAlias === null) {
		    $select = $this->select()->order(array('tree_lft', 'tree_alias'));
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
	
	private function _setExpandTree () {
		$newid = self::$_nodes[self::$_currentid]['tree_parentid'];
		self::$_nodes[self::$_currentid]['expanded'] = true;
		self::$_nodes[self::$_currentid]['active']  = true;
		while (isset(self::$_nodes[$newid])) {
			self::$_nodes[$newid]['expanded'] = true;
			$newid = self::$_nodes[$newid]['tree_parentid'];
		}
	}
	public function getChildren ($parent = 0) {
		$this->_loadTree();
		$list = array();
		if (isset(self::$_children[$parent])) {
			foreach (self::$_children[$parent] as $child) {
				$list[] = self::$_nodes[$child];
			}
		}
		$data = array(
		'table'   => $this,
		'data'    => $list,
		'stored'  => true,
		'rowClass' => $this->_rowClass
		);

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
	public function hasChildren ($parent = 0)	{
		$this->_loadTree();
		return (isset(self::$_children[$parent]));
	}

	/**
	 * Create the alias name if not exist
	 *
	 * @param string $sString
	 * @return string
	 */
	private function getRewriteString($sString)
	{
		//$string = strtolower( htmlentities( strtr($sString, array("?"=>"Ae", "?"=>"Ue", "?"=>"Oe", "?"=>"ae", "?"=>"ue", "?"=>"oe")) ) );
		$string = strtolower( htmlentities( $sString));
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
	public function find () {
		$args = func_get_args();
		$this->_loadTree();
		$list = array();
		foreach ($args as $i) {
			if (isset(self::$_nodes[$i])) {
				$list[] = self::$_nodes[$i];
			}
		}
		$data  = array(
		'table'    => $this,
		'data'     => $list,
		'rowClass' => $this->_rowClass,
		'stored'   => true
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);
	}

	/**
	 * Find a Tree Item by alias by using the cache
	 * 
	 * @param string $alias
	 * @return boolean|Jimw_Site_Tree_Rowset
	 */
	public function findAlias($alias) {
		$id = self::$_nodesAlias[$alias];
		if ($id !== false) {
			$node = isset (self::$_nodes[$id]) ? array(self::$_nodes[$id]) : array ();
			$data  = array(
			'table'    => $this,
			'data'     => $node,
			'rowClass' => $this->_rowClass,
			'stored'   => true
			);
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
	public function findByAliasWithModuleAndSite($alias) {
	    $select = $this->select()
	                   ->where('tree_alias = ?', $alias)
	                   ->where('tree_status = ?', 4)
	                   ->order(array('tree_version DESC'));
	                   
		$list = $this->_fetch($select);
		if ($list) {
			$list = $list[0];
			$module = new Jimw_Site_Module();
			$list['module'] = $module->find($list['module_id'])->current ();
			$site = new Jimw_Site_Site();
			$list['site'] = $site->find($list['site_id'])->current ();
			$data  = array(
			'table'    => $this,
			'data'     => $list,
			'rowClass' => $this->_rowClass,
			'stored'   => true
			);
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
	public function findBySite($site) {
		$this->_loadTree();
		$id = $site->tree_id;
		if (isset(self::$_nodes[$id])) {
			$list = self::$_nodes[$id];
			$module = new Jimw_Site_Module();
			$list['module'] = $module->find($list['module_id'])->current ();
			$list['site'] = $site;
			$data  = array(
			'table'    => $this,
			'data'     => $list,
			'rowClass' => $this->_rowClass,
			'stored'   => true
			);
			Zend_Loader::loadClass($this->_rowClass);
			return new $this->_rowClass($data);
		}
		return false;
	}

	public function insert(array $data)
	{
		// add a timestamp
		if (empty($data['tree_pagetitle']) && !empty($data['tree_menutitle'])) {
			$data['tree_pagetitle'] = $data['tree_menutitle'];
		} elseif (empty($data['tree_menutitle']) && !empty($data['tree_pagetitle'])) {
			$data['tree_menutitle'] = $data['tree_pagetitle'];
		}
		if (empty($data['tree_alias'])) {
			$data['tree_alias'] = $this->getRewriteString($data['tree_menutitle']);
		}
		if (empty($data['tree_status'])) {
			$data['tree_status'] = 4;
		}
		if (empty($data['tree_type'])) {
			$data['tree_type'] = 0;
		}
		if (empty($data['tree_creationdate'])) {
			$data['tree_creationdate'] = Zend_Date::now()->getIso();//new Zend_Db_Expr('NOW()');
		}
		if (empty($data['tree_editiondate'])) {
			$data['tree_editiondate'] = Zend_Date::now()->getIso();//new Zend_Db_Expr('NOW()');;
		}
		/*unset($data['tree_lft']);
		unset($data['tree_rgt']);*/
		return parent::insert($data);
	}

	/**
	 * Override the default createRow to add some default values
	 *
	 * @param array $data
	 * @return Jimw_Db_RowClass
	 */
	public function createRow(array $data = array())
	{
		$data['tree_creationdate'] = time();
		$data['tree_editiondate'] = time();
		$data['tree_type'] = 0;
		$data['tree_status'] = 4;
		$select = $this->_db->select();
		$select->from($this->_name, new Zend_Db_Expr('MAX(tree_rgt)'));
		$max = $this->_db->fetchOne($select);
		$data['tree_lft'] = $max + 1;
		$data['tree_rgt'] = $max + 2;
		return parent::createRow($data);
	}

	public function selectChildren($parent) {
	    if (!($parent instanceof  Jimw_Site_Tree)) {
			$parent = $this->find($parent)->current();
		}
	    if (!$parent) { // Root
			return $this->select()->order('tree_lft');
		}
		else {
		    return $this->select()->where('tree_lft >= ?', $parent->lft)
		           ->where('tree_rgt <= ? ', $parent->rgt)->order('tree_lft');
		}
	}
	
	public function fetchAllChildren($parent, $where = null, $order = null, $count = null, $offset = null) {
		if (!($parent instanceof  Jimw_Site_Tree)) {
			$parent = $this->find($parent)->current();
		}
		if (empty($order)) {
            $order = array('tree_lft');
        }
        if (!$parent) { // Root
			return $this->fetchAll($where, $order, $count);
		}
		else {
			return $this->fetchAll($this->_buildWhere($parent->lft, $parent->rgt, $where), $order, $count, $offset);
		}
	}
		
	public function updateAllChildren($parent, $data, $where = null) {
		if (!($parent instanceof  Jimw_Site_Tree)) {
			$parent = $this->find($parent)->current();
		}
		if (!$parent) {
			return $this->update($data, $where);
		}
		else {
			return $this->update($data, $this->_buildWhere($parent->lft, $parent->rgt, $where));
		}
	}
	
	public function _buildWhere($lft, $rgt, $where = null, $col = false) {
	    $where_array = array($this->_db->quoteInto($this->_db->quoteIdentifier(($col) ? $col : 'tree_lft', true) . ' >= ?', $lft), 
							 $this->_db->quoteInto($this->_db->quoteIdentifier(($col) ? $col : 'tree_rgt', true) . ' <= ?', $rgt));
		if (empty($where)) {
			return $where_array;
		}
		elseif (is_array($where)) {
			return array_merge($where, $where_array);
		}
		else {
			$where_array[] = $where;
			return $where_array;
		}
	}
	
	public function delete($where) {
			    /*def before_destroy
301 	          return if self[acts_as_nested_set_options[:right_column]].nil? || self[acts_as_nested_set_options[:left_column]].nil?
302 	          dif = self[acts_as_nested_set_options[:right_column]] - self[acts_as_nested_set_options[:left_column]] + 1
303 	
304 	          self.class.transaction {
305 	            self.class.delete_all( "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} > #{self[acts_as_nested_set_options[:left_column]]} and #{acts_as_nested_set_options[:right_column]} < #{self[acts_as_nested_set_options[:right_column]]}" )
306 	            self.class.update_all( "#{acts_as_nested_set_options[:left_column]} = (#{acts_as_nested_set_options[:left_column]} - #{dif})",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} >= #{self[acts_as_nested_set_options[:right_column]]}" )
307 	            self.class.update_all( "#{acts_as_nested_set_options[:right_column]} = (#{acts_as_nested_set_options[:right_column]} - #{dif} )",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:right_column]} >= #{self[acts_as_nested_set_options[:right_column]]}" )
308 	          }
309 	        end*/
		parent::delete($where);
	}
	public function update(array $data, $where)
	{
		// add a timestamp
		if (empty($data['tree_editiondate'])) {
			$data['tree_editiondate'] = Zend_Date::now()->getIso();//new Zend_Db_Expr('NOW()');
		}
		unset($data['tree_lft']);
		unset($data['tree_rgt']);
		return parent::update($data, $where);
	}
	public function update_old(array $data, $where)
	{
		return parent::update($data, $where);
	}
	
	const CHILD = 0;
	const LEFT = 1;
	const RIGHT = 2;
	 
	public function move_to(Jimw_Site_Tree_Row $tree, $target, $position) {
		$cur_left = $tree->lft;
		$cur_right = $tree->rgt;
		$extent = $cur_right - $cur_left + 1;
		if (!($target instanceof Jimw_Site_Tree_Row) && $target != 0) {
			$target = $this->find($target)->current();
		}
		if (!$target) { //Root
			//throw new Jimw_Exception('Not found');
			if ($position == self::RIGHT) {
				//Move on LEFT of last Root
				$target = $this->fetchRow(array('tree_parentid = ?' => 0), 'tree_lft ASC');
			}
			else {
				//Move on RIGHT of first Root
				$target = $this->fetchRow(array('tree_parentid = ?' => 0), 'tree_rgt DESC');
				$position = self::LEFT;
			}
			if (!$target)
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
			throw new Jimw_Exception('Position should be either child, left or right ('.$position.' received)');
		}
		if ($bound > $cur_right) {
			$bound -= 1;
			$other_bound = $cur_right + 1;
		}
		else {
			$other_bound = $cur_left - 1;
		}
		if ($bound == $cur_left || $bound == $cur_right) {
			return ;
		}
		$tab = array($cur_left, $cur_right, $bound, $other_bound);
		sort($tab);
		list($a, $b, $c, $d) = $tab;
		// Transaction for all update
		parent::update(array('tree_lft' => new Zend_Db_Expr('CASE WHEN tree_lft BETWEEN ' . $a . ' AND ' . $b . ' THEN tree_lft + ' . ($d - $b)
															. ' WHEN tree_lft BETWEEN ' . $c . ' AND ' . $d . ' THEN tree_lft + ' . ($a - $c)
															. ' ELSE tree_lft END'),
							 'tree_rgt' => new Zend_Db_Expr('CASE WHEN tree_rgt BETWEEN ' . $a . ' AND ' . $b . ' THEN tree_rgt + ' . ($d - $b)
															. ' WHEN tree_rgt BETWEEN ' . $c . ' AND ' . $d . ' THEN tree_rgt + ' . ($a - $c)
															. ' ELSE tree_rgt END')), null);
		self::$_nodes = null;
	}
}

/*
 * module SymetrieCom
2 	  module Acts #:nodoc:
3 	    module NestedSet #:nodoc:
4 	      def self.append_features(base)
5 	        super       
6 	        base.extend(ClassMethods)             
7 	      end 
8 	
9 	      # better_nested_set ehances the core nested_set tree functionality provided in ruby_on_rails.
10 	      #
11 	      # This acts provides Nested Set functionality. Nested Set is a smart way to implement
12 	      # an _ordered_ tree, with the added feature that you can select the children and all of their
13 	      # descendents with a single query. The drawback is that insertion or move need some complex
14 	      # sql queries. But everything is done here by this module!
15 	      #
16 	      # Nested sets are appropriate each time you want either an orderd tree (menus,
17 	      # commercial categories) or an efficient way of querying big trees (threaded posts).
18 	      #
19 	      # == API
20 	      # Methods names are aligned on Tree's ones as much as possible, to make replacment from one
21 	      # by another easier, except for the creation:
22 	      #
23 	      # in acts_as_tree:
24 	      #   item.children.create(:name => "child1")
25 	      #
26 	      # in acts_as_nested_set:
27 	      #   # adds a new item at the "end" of the tree, i.e. with child.left = max(tree.right)+1
28 	      #   child = MyClass.new(:name => "child1")
29 	      #   child.save
30 	      #   # now move the item to its right place
31 	      #   child.move_to_child_of my_item
32 	      #
33 	      # You can use:
34 	      # * move_to_child_of
35 	      # * move_to_right_of
36 	      # * move_to_left_of
37 	      # and pass them an id or an object.
38 	      #
39 	      # Other methods added by this mixin are:
40 	      # * +root+ - root item of the tree (the one that has a nil parent; should have left_column = 1 too)
41 	      # * +roots+ - root items, in case of multiple roots (the ones that have a nil parent)
42 	      # * +level+ - number indicating the level, a root being level 0
43 	      # * +ancestors+ - array of all parents, with root as first item
44 	      # * +self_and_ancestors+ - array of all parents and self
45 	      # * +siblings+ - array of all siblings, that are the items sharing the same parent and level
46 	      # * +self_and_siblings+ - array of itself and all siblings
47 	      # * +children_count+ - count of all immediate children
48 	      # * +children+ - array of all immediate childrens
49 	      # * +all_children+ - array of all children and nested children
50 	      # * +full_set+ - array of itself and all children and nested children
51 	      #
52 	      # These should not be useful, except if you want to write direct SQL:
53 	      # * +left_col_name+ - name of the left column passed on the declaration line
54 	      # * +right_col_name+ - name of the right column passed on the declaration line   
55 	      # * +parent_col_name+ - name of the parent column passed on the declaration line
56 	      #
57 	      # recommandations:
58 	      # Don't name your left and right columns 'left' and 'right': these names are reserved on most of dbs.
59 	      # Usage is to name them 'lft' and 'rgt' for instance.
60 	      #
61 	      module ClassMethods               
62 	        # Configuration options are:
63 	        #
64 	        # * +parent_column+ - specifies the column name to use for keeping the position integer (default: parent_id)
65 	        # * +left_column+ - column name for left boundry data, default "lft"
66 	        # * +right_column+ - column name for right boundry data, default "rgt"
67 	        # * +text_column+ - column name for the title field (optional). Used as default in the
68 	        #   {your-class}_options_for_select helper method. If empty, will use the first string field
69 	        #   of your model class.
70 	        # * +scope+ - restricts what is to be considered a list. Given a symbol, it'll attach "_id"
71 	        #   (if that hasn't been already) and use that as the foreign key restriction. It's also possible
72 	        #   to give it an entire string that is interpolated if you need a tighter scope than just a foreign key.
73 	        #   Example: <tt>acts_as_nested_set :scope => 'todo_list_id = #{todo_list_id} AND completed = 0'</tt>
74 	        def acts_as_nested_set(options = {})         
75 	          if options[:scope].is_a?(Symbol)
76 	            if options[:scope].to_s !~ /_id$/
77 	              options[:scope] = "#{options[:scope]}_id".intern
78 	            end         
79 	            options[:scope] = %(#{options[:scope].to_s}.nil? ? "#{options[:scope].to_s} IS NULL" : "#{options[:scope].to_s} = \#{#{options[:scope].to_s}}")
80 	          end
81 	
82 	#            options[:scope] = %("#{options[:scope]}")
83 	
84 	          write_inheritable_attribute(:acts_as_nested_set_options,
85 	             { :parent_column  => (options[:parent_column] || 'parent_id'),
86 	               :left_column    => (options[:left_column]   || 'lft'),
87 	               :right_column   => (options[:right_column]  || 'rgt'),
88 	               :scope          => (options[:scope] || '1 = 1'),
89 	               :text_column    => (options[:text_column] || columns.collect{|c| (c.type == :string) ? c.name : nil }.compact.first)
90 	              } )
91 	               
92 	          class_inheritable_reader :acts_as_nested_set_options
93 	       
94 	          # no bulk assignment
95 	          attr_protected  acts_as_nested_set_options[:left_column].intern,
96 	                          acts_as_nested_set_options[:right_column].intern,
97 	                          acts_as_nested_set_options[:parent_column].intern
98 	          # no assignment to structure fields
99 	          module_eval <<-"end_eval", __FILE__, __LINE__
100 	            def #{acts_as_nested_set_options[:left_column]}=(x)
101 	              raise ActiveRecord::ActiveRecordError, "Unauthorized assignment to #{acts_as_nested_set_options[:left_column]}: it's an internal field handled by acts_as_nested_set code, use move_to_* methods instead."
102 	            end
103 	            def #{acts_as_nested_set_options[:right_column]}=(x)
104 	              raise ActiveRecord::ActiveRecordError, "Unauthorized assignment to #{acts_as_nested_set_options[:right_column]}: it's an internal field handled by acts_as_nested_set code, use move_to_* methods instead."
105 	            end
106 	            def #{acts_as_nested_set_options[:parent_column]}=(x)
107 	              raise ActiveRecord::ActiveRecordError, "Unauthorized assignment to #{acts_as_nested_set_options[:parent_column]}: it's an internal field handled by acts_as_nested_set code, use move_to_* methods instead."
108 	            end
109 	          end_eval
110 	       
111 	          include SymetrieCom::Acts::NestedSet::InstanceMethods
112 	          extend SymetrieCom::Acts::NestedSet::ClassMethods
113 	         
114 	          # adds the helper for the class
115 	#          ActionView::Base.send(:define_method, "#{Inflector.underscore(self.class)}_options_for_select") { special=nil
116 	#              "#{acts_as_nested_set_options[:text_column]} || "#{self.class} id #{id}"
117 	#            }
118 	         
119 	        end       
120 	
121 	        # Returns the single root
122 	        def root
123 	          self.find(:first, :conditions => "(#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:parent_column]} IS NULL)")
124 	        end
125 	       
126 	        # Returns roots when multiple roots (or virtual root, which is the same)
127 	        def roots
128 	          self.find(:all, :conditions => "(#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:parent_column]} IS NULL)", :order => "#{acts_as_nested_set_options[:left_column]}")
129 	        end                             
130 	      end
131 	     
132 	      module InstanceMethods
133 	        def left_col_name() acts_as_nested_set_options[:left_column] end
134 	        def right_col_name() acts_as_nested_set_options[:right_column] end             
135 	        def parent_col_name() acts_as_nested_set_options[:parent_column] end
136 	
137 	        # on creation, set automatically lft and rgt to the end of the tree
138 	        def before_create
139 	          maxright = self.class.maximum(acts_as_nested_set_options[:right_column], :conditions => acts_as_nested_set_options[:scope]) || 0
140 	          # adds the new node to the right of all existing nodes
141 	          self[acts_as_nested_set_options[:left_column]] = maxright+1
142 	          self[acts_as_nested_set_options[:right_column]] = maxright+2
143 	        end
144 	
145 	        # Returns true if this is a root node.
146 	        def root?
147 	          parent_id = self[acts_as_nested_set_options[:parent_column]]
148 	          (parent_id == 0 || parent_id.nil?) && (self[acts_as_nested_set_options[:left_column]] == 1) && (self[acts_as_nested_set_options[:right_column]] > self[acts_as_nested_set_options[:left_column]])
149 	        end                                                                                             
150 	                                   
151 	        # Returns true is this is a child node
152 	        def child?                         
153 	          parent_id = self[acts_as_nested_set_options[:parent_column]]
154 	          !(parent_id == 0 || parent_id.nil?) && (self[acts_as_nested_set_options[:left_column]] > 1) && (self[acts_as_nested_set_options[:right_column]] > self[acts_as_nested_set_options[:left_column]])
155 	        end     
156 	       
157 	        # Returns true if we have no idea what this is
158 	        #
159 	        # Deprecated, will be removed in next versions
160 	        def unknown?
161 	          !root? && !child?
162 	        end
163 	       
164 	        # order by left column
165 	        def <=>(x)
166 	          self[acts_as_nested_set_options[:left_column]] <=> x[acts_as_nested_set_options[:left_column]]
167 	        end
168 	
169 	        # Adds a child to this object in the tree.  If this object hasn't been initialized,
170 	        # it gets set up as a root node.  Otherwise, this method will update all of the
171 	        # other elements in the tree and shift them to the right, keeping everything
172 	        # balanced.
173 	        #
174 	        # Deprecated, will be removed in next versions
175 	        def add_child( child )     
176 	          self.reload
177 	          child.reload
178 	
179 	          if child.root?
180 	            raise ActiveRecord::ActiveRecordError, "Adding sub-tree isn\'t currently supported"
181 	          else
182 	            if ( (self[acts_as_nested_set_options[:left_column]] == nil) || (self[acts_as_nested_set_options[:right_column]] == nil) )
183 	              # Looks like we're now the root node!  Woo
184 	              self[acts_as_nested_set_options[:left_column]] = 1
185 	              self[acts_as_nested_set_options[:right_column]] = 4
186 	             
187 	              # What do to do about validation?
188 	              return nil unless self.save
189 	             
190 	              child[acts_as_nested_set_options[:parent_column]] = self.id
191 	              child[acts_as_nested_set_options[:left_column]] = 2
192 	              child[acts_as_nested_set_options[:right_column]]= 3
193 	              return child.save
194 	            else
195 	              # OK, we need to add and shift everything else to the right
196 	              child[acts_as_nested_set_options[:parent_column]] = self.id
197 	              right_bound = self[acts_as_nested_set_options[:right_column]]
198 	              child[acts_as_nested_set_options[:left_column]] = right_bound
199 	              child[acts_as_nested_set_options[:right_column]] = right_bound + 1
200 	              self[acts_as_nested_set_options[:right_column]] += 2
201 	              self.class.transaction {
202 	                self.class.update_all( "#{acts_as_nested_set_options[:left_column]} = (#{acts_as_nested_set_options[:left_column]} + 2)",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} >= #{right_bound}" )
203 	                self.class.update_all( "#{acts_as_nested_set_options[:right_column]} = (#{acts_as_nested_set_options[:right_column]} + 2)",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:right_column]} >= #{right_bound}" )
204 	                self.save
205 	                child.save
206 	              }
207 	            end
208 	          end                                   
209 	        end
210 	         
211 	        # Returns root
212 	        def root
213 	            self.class.find(:first, :conditions => "#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:parent_column]} IS NULL)")
214 	        end
215 	               
216 	        # Returns roots when multiple roots (or virtual root, which is the same)
217 	        def roots
218 	            self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:parent_column]} IS NULL)", :order => "#{acts_as_nested_set_options[:left_column]}")
219 	        end
220 	               
221 	        # Returns the parent
222 	        def parent
223 	            self.class.find(self[acts_as_nested_set_options[:parent_column]]) if self[acts_as_nested_set_options[:parent_column]]
224 	        end
225 	       
226 	        # Returns an array of all parents
227 	        # Maybe 'full_outline' would be a better name, but we prefer to mimic the Tree class
228 	        def ancestors
229 	            self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:left_column]} < #{self[acts_as_nested_set_options[:left_column]]} and #{acts_as_nested_set_options[:right_column]} > #{self[acts_as_nested_set_options[:right_column]]})", :order => acts_as_nested_set_options[:left_column] )
230 	        end
231 	       
232 	        # Returns the array of all parents and self
233 	        def self_and_ancestors
234 	            ancestors + [self]
235 	        end
236 	       
237 	        # Returns the array of all children of the parent, except self
238 	        def siblings
239 	            self_and_siblings - [self]
240 	        end
241 	       
242 	        # Returns the array of all children of the parent, included self
243 	        def self_and_siblings
244 	            if self[acts_as_nested_set_options[:parent_column]].nil? || self[acts_as_nested_set_options[:parent_column]].zero?
245 	                [self]
246 	            else
247 	                self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} and #{acts_as_nested_set_options[:parent_column]} = #{self[acts_as_nested_set_options[:parent_column]]}", :order => acts_as_nested_set_options[:left_column])
248 	            end
249 	        end
250 	       
251 	        # Returns the level of this object in the tree
252 	        # root level is 0
253 	        def level
254 	            return 0 if self[acts_as_nested_set_options[:parent_column]].nil?
255 	            self.class.count("#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:left_column]} < #{self[acts_as_nested_set_options[:left_column]]} and #{acts_as_nested_set_options[:right_column]} > #{self[acts_as_nested_set_options[:right_column]]})")
256 	        end                                 
257 	                                           
258 	        # Returns the number of nested children of this object.
259 	        def children_count
260 	          return (self[acts_as_nested_set_options[:right_column]] - self[acts_as_nested_set_options[:left_column]] - 1)/2
261 	        end
262 	                                                               
263 	        # Returns a set of itself and all of its nested children
264 	        # Pass :exclude => item, or id, or [items or id] to exclude some parts of the tree
265 	        def full_set(special=nil)
266 	          return [self] if new_record? or self[acts_as_nested_set_options[:right_column]]-self[acts_as_nested_set_options[:left_column]] == 1
267 	#          self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:left_column]} BETWEEN #{self[acts_as_nested_set_options[:left_column]]} and #{self[acts_as_nested_set_options[:right_column]]})", :order => acts_as_nested_set_options[:left_column])
268 	          [self] + all_children(special)
269 	        end
270 	                 
271 	        # Returns a set of all of its children and nested children
272 	        # Pass :exclude => item, or id, or [items or id] to exclude some parts of the tree
273 	        def all_children(special=nil)
274 	          if special && special[:exclude]
275 	            transaction do
276 	              # exclude some items and all their children
277 	              special[:exclude] = [special[:exclude]] if !special[:exclude].is_a?(Array)
278 	              # get objects for ids
279 	              special[:exclude].collect! {|s| s.is_a?(self.class) ? s : self.class.find(s)}
280 	              # get all subtrees and flatten the list
281 	              exclude_list = special[:exclude].map{|e| e.full_set.map{|ee| ee.id}}.flatten.uniq.join(',')
282 	              if exclude_list.blank?
283 	                self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:left_column]} > #{self[acts_as_nested_set_options[:left_column]]}) and (#{acts_as_nested_set_options[:right_column]} < #{self[acts_as_nested_set_options[:right_column]]})", :order => acts_as_nested_set_options[:left_column])
284 	              else
285 	                self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND id NOT IN (#{exclude_list}) AND (#{acts_as_nested_set_options[:left_column]} > #{self[acts_as_nested_set_options[:left_column]]}) and (#{acts_as_nested_set_options[:right_column]} < #{self[acts_as_nested_set_options[:right_column]]})", :order => acts_as_nested_set_options[:left_column])
286 	              end
287 	            end
288 	          else
289 	            self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND (#{acts_as_nested_set_options[:left_column]} > #{self[acts_as_nested_set_options[:left_column]]}) and (#{acts_as_nested_set_options[:right_column]} < #{self[acts_as_nested_set_options[:right_column]]})", :order => acts_as_nested_set_options[:left_column])
290 	          end
291 	        end
292 	
293 	        # Returns a set of only this entry's immediate children
294 	        def children
295 	            self.class.find(:all, :conditions => "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:parent_column]} = #{self.id}", :order => acts_as_nested_set_options[:left_column])
296 	        end
297 	                                     
298 	        # Prunes a branch off of the tree, shifting all of the elements on the right
299 	        # back to the left so the counts still work.
300 	        def before_destroy
301 	          return if self[acts_as_nested_set_options[:right_column]].nil? || self[acts_as_nested_set_options[:left_column]].nil?
302 	          dif = self[acts_as_nested_set_options[:right_column]] - self[acts_as_nested_set_options[:left_column]] + 1
303 	
304 	          self.class.transaction {
305 	            self.class.delete_all( "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} > #{self[acts_as_nested_set_options[:left_column]]} and #{acts_as_nested_set_options[:right_column]} < #{self[acts_as_nested_set_options[:right_column]]}" )
306 	            self.class.update_all( "#{acts_as_nested_set_options[:left_column]} = (#{acts_as_nested_set_options[:left_column]} - #{dif})",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:left_column]} >= #{self[acts_as_nested_set_options[:right_column]]}" )
307 	            self.class.update_all( "#{acts_as_nested_set_options[:right_column]} = (#{acts_as_nested_set_options[:right_column]} - #{dif} )",  "#{acts_as_nested_set_options[:scope]} AND #{acts_as_nested_set_options[:right_column]} >= #{self[acts_as_nested_set_options[:right_column]]}" )
308 	          }
309 	        end
310 	       
311 	        # Move the node to the left of another node (you can pass id only)
312 	        def move_to_left_of(node)
313 	            self.move_to node, :left
314 	        end
315 	       
316 	        # Move the node to the left of another node (you can pass id only)
317 	        def move_to_right_of(node)
318 	            self.move_to node, :right
319 	        end
320 	       
321 	        # Move the node to the child of another node (you can pass id only)
322 	        def move_to_child_of(node)
323 	            self.move_to node, :child
324 	        end
325 	       
326 	        protected
327 	        def move_to(target, position)
328 	          raise ActiveRecord::ActiveRecordError, "You cannot move a new node" if self.id.nil?
329 	       
330 	          # use shorter names for readability: current left and right
331 	          cur_left, cur_right = self[acts_as_nested_set_options[:left_column]], self[acts_as_nested_set_options[:right_column]]
332 	             
333 	          # extent is the width of the tree self and children
334 	          extent = cur_right - cur_left + 1
335 	         
336 	          # load object if node is not an object
337 	          if !(self.class === target)
338 	            target = self.class.find(target)
339 	          end
340 	          target_left, target_right = target[acts_as_nested_set_options[:left_column]], target[acts_as_nested_set_options[:right_column]]
341 	
342 	          # detect impossible move
343 	          if ((cur_left <= target_left) && (target_left <= cur_right)) or ((cur_left <= target_right) && (target_right <= cur_right))
344 	            raise ActiveRecord::ActiveRecordError, "Impossible move, target node cannot be inside moved tree."
345 	          end
346 	       
347 	          # compute new left/right for self
348 	          if position == :child
349 	            if target_left < cur_left
350 	              new_left  = target_left + 1
351 	              new_right = target_left + extent
352 	            else
353 	              new_left  = target_left - extent + 1
354 	              new_right = target_left
355 	            end
356 	          elsif position == :left
357 	            if target_left < cur_left
358 	              new_left  = target_left
359 	              new_right = target_left + extent - 1
360 	            else
361 	              new_left  = target_left - extent
362 	              new_right = target_left - 1
363 	            end
364 	          elsif position == :right
365 	            if target_right < cur_right
366 	              new_left  = target_right + 1
367 	              new_right = target_right + extent
368 	            else
369 	              new_left  = target_right - extent + 1
370 	              new_right = target_right
371 	            end
372 	          else
373 	            raise ActiveRecord::ActiveRecordError, "Position should be either left or right ('#{position}' received)."
374 	          end
375 	         
376 	          # boundaries of update action
377 	          b_left, b_right = [cur_left, new_left].min, [cur_right, new_right].max
378 	         
379 	          # Shift value to move self to new position
380 	          shift = new_left - cur_left
381 	         
382 	          # Shift value to move nodes inside boundaries but not under self_and_children
383 	          updown = (shift > 0) ? -extent : extent
384 	         
385 	          # change nil to NULL for new parent
386 	          if position == :child
387 	            new_parent = target.id
388 	          else
389 	            new_parent = target[acts_as_nested_set_options[:parent_column]].nil? ? 'NULL' : target[acts_as_nested_set_options[:parent_column]]
390 	          end
391 	         
392 	          # update and that rules
393 	          self.class.update_all( "#{acts_as_nested_set_options[:left_column]} = CASE \
394 	                                      WHEN #{acts_as_nested_set_options[:left_column]} BETWEEN #{cur_left} AND #{cur_right} \
395 	                                        THEN #{acts_as_nested_set_options[:left_column]} + #{shift} \
396 	                                      WHEN #{acts_as_nested_set_options[:left_column]} BETWEEN #{b_left} AND #{b_right} \
397 	                                        THEN #{acts_as_nested_set_options[:left_column]} + #{updown} \
398 	                                      ELSE #{acts_as_nested_set_options[:left_column]} END, \
399 	                                  #{acts_as_nested_set_options[:right_column]} = CASE \
400 	                                      WHEN #{acts_as_nested_set_options[:right_column]} BETWEEN #{cur_left} AND #{cur_right} \
401 	                                        THEN #{acts_as_nested_set_options[:right_column]} + #{shift} \
402 	                                      WHEN #{acts_as_nested_set_options[:right_column]} BETWEEN #{b_left} AND #{b_right} \
403 	                                        THEN #{acts_as_nested_set_options[:right_column]} + #{updown} \
404 	                                      ELSE #{acts_as_nested_set_options[:right_column]} END, \
405 	                                  #{acts_as_nested_set_options[:parent_column]} = CASE \
406 	                                      WHEN #{self.class.primary_key} = #{self.id} \
407 	                                        THEN #{new_parent} \
408 	                                      ELSE #{acts_as_nested_set_options[:parent_column]} END",
409 	                                  acts_as_nested_set_options[:scope] )
410 	          self.reload
411 	        end
412 	       
413 	      end
414 	     
415 	    end
416 	  end
417 	end*/