<?php

require_once ('Zend/Navigation/Page.php');

class Jimw_Site_Tree_Navigation extends Zend_Navigation_Page {
	/**
     * Cached href
     *
     * The use of this variable minimizes execution time when getHref() is
     * called more than once during the lifetime of a request. If a property
     * is updated, the cache is invalidated.
     *
     * @var string
     */
    protected $_hrefCache;

    /**
     * Action helper for assembling URLs
     *
     * @see getHref()
     * @var Zend_Controller_Action_Helper_Url
     */
    protected static $_urlHelper = null;
	/**
	 * Tree
	 *
	 * @var Jimw_Site_Tree_Row
	 */
	private $tree;
	
	/*public function __construct(Jimw_Site_Tree_Row $tree, $config = null) {
		parent::__construct($config);
		$this->tree = $tree;
		$this->addChildren($config);
	}*/
	
	
	/**
	 * Add all children of the page from the Tree
	 * @param $config
	 * @return unknown_type
	 */
	protected function addChildren() {
	    if ($this->tree->hasChildren()) {
    		$children = $this->tree->getChildren();
    		foreach ($children as $child) {
    			$this->addPage(new Jimw_Site_Tree_Navigation(array('tree' => $child)));
    		}
	    }
	}
	
	/**
	 * @see Zend_Navigation_Page::getHref()
	 *
	 * @return string
	 */
	public function getHref() {
		        if ($this->_hrefCache) {
            return $this->_hrefCache;
        }

        if (null === self::$_urlHelper) {
            self::$_urlHelper =
                new Jimw_Site_View_Helper_Url();            
        }

        $params = array('alias' => $this->tree->alias);
        
        $url = self::$_urlHelper->url($params,
                                      'alias',
                                      true);

        return $this->_hrefCache = $url;
	}
	
	public function getAlias() {
	     return $this->getTree()->alias;   
	}
	
	/**
	 * @see Zend_Navigation_Page::getActive()
	 *
	 * @param bool $recursive
	 * @return bool
	 */
	public function getActive($recursive) {
		if (!parent::getActive($recursive))
			return $this->tree->getActive();
		return true;
	}
	
	/**
	 * @see Zend_Navigation_Page::getLabel()
	 *
	 * @return string
	 */
	public function getLabel() {
		return $this->tree->menutitle;
	}
	
	/**
	 * @see Zend_Navigation_Page::getTitle()
	 *
	 * @return string|null
	 */
	public function getTitle() {
		return $this->tree->pagetitle;
	}
	
	/**
	 * @see Zend_Navigation_Page::getVisible()
	 *
	 * @param bool $recursive
	 * @return bool
	 */
	public function getVisible($recursive) {
		//if (parent::getVisible($recursive))
			return $this->tree->isVisible();
		return false;
	}
	
	/**
	 * @see Zend_Navigation_Page::isActive()
	 *
	 * @param bool $recursive
	 * @return bool
	 */
	public function isActive($recursive) {
		//if (!parent::getActive())
			if ($recursive)
				return $this->tree->getExpanded();
			else
				return $this->tree->getActive();
		return true;
	}
	
	/**
	 * @see Zend_Navigation_Page::isVisible()
	 *
	 * @param bool $recursive
	 * @return bool
	 */
	public function isVisible($recursive) {
		return $this->getVisible();
	}
	
	/**
	 * @return Jimw_Site_Tree_Row
	 */
	public function getTree() {
		return $this->tree;
	}
	
	/**
	 * @param Jimw_Site_Tree_Row $tree
	 */
	public function setTree($tree) {
		$this->tree = $tree;
		$this->addChildren();
	}
	

    /**
     * Returns an array representation of the page
     *
     * @return array  associative array containing all page properties
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            array(
                'tree'       => $this->tree->toArray()
            ));
    }
    
    /**
     * Get a variable
     *
     * @param String $name the name of the parameter
     * @return mixed
     */
    public function __get($name) {
        if ($name == 'lastmod') {
            $date = new Zend_Date($this->tree->editiondate);
            return $date->toString(Zend_Date::W3C);
        }
        $value = parent::__get($name);
        if ($value === null) {
            $value = $this->tree->__get($name);
        }
        return $value;
    }
    
    public function __isset($name) {
    	if ($name == 'lastmod' && isset($this->tree->editiondate))
    		return true;
    	if ($this->tree->__isset($name))
    		return true;
    	return parent::__isset($name);
    }
    
    public function __call($method, array $arguments) {
    	if (strpos($name, 'find') === false)
    		return parent::__call($method, $arguments);
    	return call_user_func_array(array($this->tree,$method), $arguments);	
    }
}

?>