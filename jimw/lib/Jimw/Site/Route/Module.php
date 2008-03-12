<?php
/**
 * Jimw_Site_Route_Module
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Route_Module extends Zend_Controller_Router_Route_Module {

	/**
     * @const string EXT delimiter
     */
    const EXT_DELIMITER = '.';
    
	protected $_extKey  = 'ext';
	
	protected $_validModule = true;

    /**
     * Constructor
     *
     * @param array Defaults for map variables with keys as variable names
     * @param Zend_Controller_Dispatcher_Interface Dispatcher object
     * @param Zend_Controller_Request_Abstract Request object
     */
    public function __construct(array $defaults = array(),
                Zend_Controller_Dispatcher_Interface $dispatcher = null,
                Zend_Controller_Request_Abstract $request = null,
                $validModule = true)
    {
        parent::__construct($defaults, $dispatcher, $request);
        $this->_validModule = $validModule;
    }
	
    /**
     * Set request keys based on values in request object
     *
     * @return void
     */
    protected function _setRequestKeys()
    {
        if (null !== $this->_request) {
            $this->_moduleKey     = $this->_request->getModuleKey();
            $this->_controllerKey = $this->_request->getControllerKey();
            $this->_actionKey     = $this->_request->getActionKey();
            $this->_extKey        = $this->_request->getExtKey();
        }

        if (null !== $this->_dispatcher) {
            $this->_defaults += array(
                $this->_controllerKey => $this->_dispatcher->getDefaultControllerName(),
                $this->_actionKey     => $this->_dispatcher->getDefaultAction(),
                $this->_moduleKey     => $this->_dispatcher->getDefaultModule(),
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
            
            /*if ($path[0] == JIMW_URL_MODULE_PATH) {
                array_shift($path);
            } else {
                return false;
            }*/
			
            if ($this->_dispatcher && $path[0] == JIMW_URL_MODULE_PATH && $this->_dispatcher->isValidModule($path[1])) {
                array_shift($path);
                $values[$this->_moduleKey] = array_shift($path);
                $this->_moduleValid = true;
            }
            elseif ($this->_validModule) {            	
            	return false;
            }

        	if (($l = count($path)) > 0 && ($pos = strrpos($path[$l -1], self::EXT_DELIMITER)) !== false && $pos >= 0) {
            	$values[$this->_extKey] = substr($path[$l - 1], $pos + 1);
            	$path[$l - 1] = substr($path[$l - 1], 0, $pos);
            }
            
            if (count($path) && !empty($path[0])) {
                $values[$this->_controllerKey] = array_shift($path);
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_actionKey] = array_shift($path);
            }

            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = $val;
                }
            }
        }

        $this->_values = $values + $params;
        return $this->_values + $this->_defaults;
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

        if ($this->_moduleValid || array_key_exists($this->_moduleKey, $data)) {
            if ($params[$this->_moduleKey] != $this->_defaults[$this->_moduleKey]) {
                $module = $params[$this->_moduleKey];
            }
        }
        unset($params[$this->_moduleKey]);

        $controller = $params[$this->_controllerKey];
        unset($params[$this->_controllerKey]);

        $action = $params[$this->_actionKey];
        unset($params[$this->_actionKey]);

        $ext = $params[$this->_extKey];
        unset($params[$this->_extKey]);
             
        foreach ($params as $key => $value) {
            $url .= self::URI_DELIMITER . $key;
            $url .= self::URI_DELIMITER . $value;
        }
        
        if (!empty($url) || $action !== $this->_defaults[$this->_actionKey]) {
            $url = self::URI_DELIMITER . $action . $url;
        }

        if (!empty($url) || $controller !== $this->_defaults[$this->_controllerKey]) {
            $url = self::URI_DELIMITER . $controller . $url;
        }

        if (isset($module)) {        
            $url = self::URI_DELIMITER . $module . $url;
            if ($module != JIMW_URL_DEFAULT_PATH) {
		       $url = self::URI_DELIMITER . JIMW_URL_MODULE_PATH . $url;
		    }
        }
        
		
        $url = rtrim($url, self::URI_DELIMITER);
        
    	if (!empty($url) || $ext !== $this->_defaults[$this->_extKey]) {
            $url .= self::EXT_DELIMITER . $ext;
        }
        
        return $url;
    }
}

?>
