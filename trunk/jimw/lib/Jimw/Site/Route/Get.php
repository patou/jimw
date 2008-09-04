<?php

  class Jimw_Site_Route_Get extends Zend_Controller_Router_Route_Module {

  	/**
     * @const string GET delimiter
     */
    const GET_DELIMITER   = '?';
    const VAR_DELIMITER   = '&';
    const VALUE_DELIMITER = '=';
    const URI_DELIMITER   = '/';

	protected $_extKey  = 'format';
	protected $_aliasKey  = 'format';

	protected $_filename = '';
    /**
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

	protected function getFilename () {
		return $this->_filename;
	}

  	protected function setFilename ($filename) {
  		$this->_filename = $filename;
		return $this;
	}
    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance(Zend_Config $config)
    {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs);
    }

	/**
     * Prepares the route for mapping.
     *
     * @param string Map used to match with later submitted URL path
     * @param array Defaults for map variables with keys as variable names
     */
    public function __construct($defaults = array(),
                Zend_Controller_Dispatcher_Interface $dispatcher = null,
                Zend_Controller_Request_Abstract $request = null, $filename = '')
    {
        $this->_filename = $filename;
        $this->_defaults = (array) $defaults;

    	if (isset($request)) {
            $this->_request = $request;
        }

        if (isset($dispatcher)) {
            $this->_dispatcher = $dispatcher;
        }
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
	            $this->_aliasKey     = $this->_request->getAliasKey();
	            $this->_extKey        = $this->_request->getExtKey();
	        }

	        if (null !== $this->_dispatcher) {
	            $this->_defaults += array(
	                $this->_controllerKey => $this->_dispatcher->getDefaultControllerName(),
	                $this->_actionKey     => $this->_dispatcher->getDefaultAction(),
	                $this->_aliasKey     => $this->_dispatcher->getDefaultAlias(),
	                $this->_moduleKey     => $this->_dispatcher->getDefaultModule(),
	                $this->_extKey        => $this->_dispatcher->getDefaultExt()
	            );
	        }

	        $this->_keysSet = true;
	  }

  	  /**
  	   * Assemble all var to a standart query stings
  	   *
  	   * @param array $data
  	   * @param boolean $reset
  	   * @return string
  	   */
	  public  function assemble($data = array(), $reset = false, $encode = false)
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
	    foreach ($params as $key => $value) {
            $url .= self::VAR_DELIMITER . $key;
            $url .= self::VALUE_DELIMITER . rawurlencode($value);
        }

        return $this->_filename . self::GET_DELIMITER . ltrim($url, self::VAR_DELIMITER);
	  }
	  /**
	   * Match the route if the controller var exists and get all get vars
	   *
	   * @param string $path
	   * @return array|boolean
	   */
	  public function match($path) {
	       $this->_setRequestKeys();

	        $values = array();
	        $params = array();
	        $path   = trim($path, self::URI_DELIMITER);

	        if (isset($_GET[$this->_controllerKey]) || isset($_GET[$this->_aliasKey])) {
		        $this->_values = $_GET + $params;

		        return $this->_values + $this->_defaults;
	        }
	        else {
	        	return false;
	        }
	  }
    /**
     * Return a single parameter of route's defaults
     *
     * @param name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }
}
?>
