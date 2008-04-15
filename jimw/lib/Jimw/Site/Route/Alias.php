<?php
/**
 * Jimw_Site_Route_Alias
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
  class Jimw_Site_Route_Alias extends Zend_Controller_Router_Route_Module {

	/**
     * @const string EXT delimiter
     */
    const EXT_DELIMITER = '.';
    
	protected $_extKey     = 'ext';
	protected $_aliasKey     = 'alias';
	
    /**
     * Set request keys based on values in request object
     *
     * @return void
     */
    protected function _setRequestKeys()
    {
        if (null !== $this->_request) {
            $this->_aliasKey     = $this->_request->getAliasKey();
            $this->_extKey     = $this->_request->getExtKey();
        }

        if (null !== $this->_dispatcher) {
            $this->_defaults += array(
                $this->_controllerKey => $this->_dispatcher->getDefaultControllerName(),
                $this->_actionKey     => $this->_dispatcher->getDefaultAction(),
                $this->_moduleKey     => $this->_dispatcher->getDefaultModule(),
                $this->_aliasKey     => '',
                $this->_extKey        => $this->_dispatcher->getDefaultExt()
            );
        }

        $this->_keysSet = true;
    }
    /**
     * Matches a user submitted path. Assigns and returns an array of variables
     * on a successful match.
     *
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param string Path used to match against this routing map
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $this->_setRequestKeys();

        $values = array();
        $params = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);
			
            if (($l = count($path)) > 0 && ($pos = strrpos($path[$l -1], self::EXT_DELIMITER)) !== false && $pos >= 0) {
            	$this->_values[$this->_extKey] = substr($path[$l - 1], $pos + 1);
            	$this->_values[$this->_aliasKey] = substr($path[$l - 1], 0, $pos);
            }
            else {
            	$this->_values[$this->_aliasKey] = $path[$l - 1];
            }
          return $this->_values + $this->_defaults;
    	}
    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param array An array of variable and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false)
    {
        if (!$this->_keysSet) {
            $this->_setRequestKeys();
        }

        $params = (!$reset) ? $this->_values : array();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $params[$key] = $value;
            } elseif (isset($params[$key])) {
                unset($params[$key]);
            }
        }

        $params += $this->_defaults;
        
        $url = '';
        $trees = new Jimw_Site_Tree();
        if (!isset($params[$this->_aliasKey]))
        	throw new Jimw_Exception('The alias key didn\'t exist');
   		$tree = $trees->findAlias($params[$this->_aliasKey]);
		if (!$tree)
			return $url;
		if (!count($tree))
			$tree = $trees->fetchAll(array('tree_alias = ?' => $params[$this->_aliasKey]));
		$tree = $tree->current ();
        $url = rtrim($tree->alias, self::URI_DELIMITER);
		$ext = $params[$this->_extKey];
        if (!empty($ext) && $ext !== $this->_defaults[$this->_extKey]) {
        	$url .= self::EXT_DELIMITER . $ext;	
        }
        else {
        	$url .= self::URI_DELIMITER;
        }
        while (($tree = $tree->getParent()->current ()) !== null) {
        	$url = $tree->alias . self::URI_DELIMITER . $url;
        }
        return $url;
    }
}
?>
