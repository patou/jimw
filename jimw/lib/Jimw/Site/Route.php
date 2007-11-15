<?php
/**
 * Jimw_Site_Route
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 class Jimw_Site_Route  implements Zend_Controller_Router_Route_Interface{
    /**
     * @const string URI delimiter
     */
    const URI_DELIMITER = '/';

    /**
     * Default values for the route (ie. module, controller, action, params)
     * @var array
     */
    protected $_defaults;

    protected $_values      = array();
    protected $_moduleValid = false;
    protected $_keysSet     = false;

    /**#@+
     * Array keys to use for module, controller, and action. Should be taken out of request.
     * @var string
     */
    protected $_moduleKey     = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey     = 'action';
    /**#@-*/

    /**
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance(Zend_Config $config)
    {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($defs);
    }

    /**
     * Constructor
     *
     * @param array Defaults for map variables with keys as variable names
     * @param Zend_Controller_Dispatcher_Interface Dispatcher object
     * @param Zend_Controller_Request_Abstract Request object
     */
    public function __construct(array $defaults = array(),
                Zend_Controller_Dispatcher_Interface $dispatcher = null,
                Zend_Controller_Request_Abstract $request = null)
    {
        $this->_defaults = $defaults;

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
        }

        if (null !== $this->_dispatcher) {
            $this->_defaults += array(
                $this->_controllerKey => $this->_dispatcher->getDefaultControllerName(),
                $this->_actionKey     => $this->_dispatcher->getDefaultAction(),
                $this->_moduleKey     => $this->_dispatcher->getDefaultModule()
            );
        }

        $this->_keysSet = true;
    }
  	/**
	 * Get the controller name
	 */
	private function getController($path, Jimw_Global_Request $request) {
		$tab = explode('.', $path);
		if (isset($tab[1]))
			$request->setParam('ext', $tab[1]);
		$tab_path = explode('_', $tab[0]);
		$nb = count($tab_path);
		for ($i = 1; $i < $nb; $i += 2) {
			$key = $tab_path[$i];
			if ($i + 1 < $nb)
				$value = $tab_path[$i + 1];
			else
				$value = true;
			$request->setParam($key, $value);
		}
		return $tab_path[0];
	}

	private function _setPath($tab_path, $length, Jimw_Global_Request $request) {
		if ($length > 0) {
			$path = implode('/', array_slice($tab_path, 0, $length));
			$base = $request->getBasePath ();
			if (!empty($base))
				$path = $base . '/' . $path;
			$request->setPath(trim($path, '/'));
		}
	}

	/**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @param  Zend_Controller_Request_Abstract
     * @throws Jimw_Global_Exception
     * @return Zend_Controller_Request_Abstract|boolean
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
    	if (!$request instanceof Jimw_Global_Request) {
            throw new Zend_Controller_Router_Exception('Jimw_Global_Router requires a Jimw_Global_Request-based request object');
        }
        /* @var $request Jimw_Global_Request */
    	// Just One module for the first version
    	//echo $request->getBaseUrl () . '<br />';
    	$path = $request->getPathInfo ();
    	//echo $path . '<br />';
    	$len_path = strlen($path);
    	$tab_path = explode('/', trim($path, '/'));
    	$len_tab_path = count($tab_path);
    	if ($path[$len_path - 1] == '/') {
    		$controller = false;
    		if ($len_tab_path == 0)
    		{
    			$request->setModuleName('default');
    			$request->setControllerName('index');
    			$request->setActionName('index');
    			$request->setPath(trim($request->getBaseUrl(), '/'));
    		}
    		else {
    			$alias = $tab_path[$len_tab_path - 1];
    			$this->_setPath($tab_path, $len_tab_path - 1, $request);
    		}
    	}
    	else {
       		if ($len_tab_path == 1 || strpos($tab_path[$len_tab_path - 1], '.') === false)
    		{
    			$alias = $tab_path[$len_tab_path - 1];
    			$controller = false;
    			$this->_setPath($tab_path, $len_tab_path - 1, $request);
    		}
    		else {
    			$alias = $tab_path[$len_tab_path - 2];
    			$controller = $this->getController($tab_path[$len_tab_path - 1], $request);
    			$this->_setPath($tab_path, $len_tab_path - 2, $request);
    		}
    	}
    	/*echo 'alias = ', $alias, '<br />';
    	echo 'controler = ', $controller, '<br />';
    	echo 'len_tab_path = ', $len_tab_path, '<br />';
    	Zend_Debug::dump($tab_path);*/
    	$module = $this->getModuleAlias($alias, $request);
    	if ($module === false) {
    		$request->setModuleName('default');
    		$request->setControllerName('index');
    		$request->setActionName('index');
    		$this->_setPath($tab_path, $len_tab_path, $request);
    	}
    	else {
	    	$request->setPageAlias($alias);
	    	$request->setModuleName($module);
	    	if ($controller === false)
	    		$request->setControllerName($module);
	    	else
	    		$request->setControllerName($controller);
	    	$request->setActionName($alias);
    	}
    	//$this->getSite($request);
    	//Zend_Debug::dump($request);
       	return $request;
    }
  
  /**
   * 
   * @see Zend_Controller_Router_Route_Interface::assemble()
   */
  public  function assemble($data = array()) {
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
        }

        return ltrim($url, self::URI_DELIMITER);
  }
  
  /**
   * 
   * @see Zend_Controller_Router_Route_Interface::match()
   */
  public static function match ($path) {
        $this->_setRequestKeys();

        $values = array();
        $params = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);

            if ($this->_dispatcher && $this->_dispatcher->isValidModule($path[0])) {
                $values[$this->_moduleKey] = array_shift($path);
                $this->_moduleValid = true;
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
}


?>
