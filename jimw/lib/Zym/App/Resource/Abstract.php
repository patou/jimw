<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Config
 */
require_once'Zend/Config.php';

/**
 * @see Zym_App
 */
require_once 'Zym/App.php';

/**
 * Abstract resource class
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_App_Resource_Abstract
{
    /**
     * Low priority
     * 
     * Executes last
     *
     */
    const PRIORITY_LOW    = 50;
    
    /**
     * Middle priority
     * 
     * Executes with medium priority
     */
    const PRIORITY_MEDIUM = 25;
    
    /**
     * High priority
     * 
     * Executes the first
     */
    const PRIORITY_HIGH   = 10;
   
    /**
     * Application
     *
     * @var Zym_App
     */
    protected $_app;

    /**
     * Zend_Config
     *
     * @var Zend_Config
     */
    protected $_config;

    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEVELOPMENT => array(),
        Zym_App::ENV_PRODUCTION  => array(),
        Zym_App::ENV_TEST        => array(),
        Zym_App::ENV_DEFAULT     => array()
    );
    
    /**
     * Default config object cache
     *
     * @var Zend_Config
     */
    private $_defaultConfigObject;

    /**
     * Dispatch priority
     *
     * @var integer
     */
    protected $_priority = self::PRIORITY_MEDIUM;
    
    /**
     * Construct
     * 
     */
    public function __construct(Zym_App $app, Zend_Config $config = null, $environment = null)
    {
        // Set app
        $this->setApp($app);
        
        // Set config
        if ($config) {
            $this->setConfig($config, $environment);
        }
        
        // Extension api
        $this->init();
    }
    
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {}

    /**
     * Dispatch the setup process
     *
     */
    public function dispatch()
    {        
        $config = $this->getConfig();
        
        $this->preSetup($config);
        $this->setup($config);
        $this->postSetup($config);
    }
    
    /**
     * Runs before setup() in order to do pre-setup routines
     *
     */
    public function preSetup(Zend_Config $config)
    {}

    /**
     * Sets up the resource
     *
     */
    public function setup(Zend_Config $config)
    {}
    
    /**
     * Runs after setup
     *
     */
    public function postSetup(Zend_Config $config)
    {}

    /**
     * Return a Zend_Config object populated with appropriate properties and
     * reasonable default values for this resource type.
     *
     * @param string $environment
     * @return Zend_Config
     */
    public function getDefaultConfig($environment = null)
    {
        // Cache config obj
        if (!$this->_defaultConfigObject instanceof Zend_Config) {
            // Set default environment if environment doesn't exist
            if (!array_key_exists($environment, $this->_defaultConfig)) {
                $environment = Zym_App::ENV_DEFAULT;
            }
            
            // Get environment config
            $config = isset($this->_defaultConfig[$environment]) 
                        ? $this->_defaultConfig[$environment] : array();
                        
            // Merge environment with default environment
            $isNotDefaultEnv = ($environment !== Zym_App::ENV_DEFAULT);
            $isValidEnv      = array_key_exists(Zym_App::ENV_DEFAULT, $this->_defaultConfig);
            if ($isNotDefaultEnv && $isValidEnv) {
                $config = $this->_arrayMergeRecursiveOverwrite($this->_defaultConfig[Zym_App::ENV_DEFAULT], $config);
            }
            
            $this->_defaultConfigObject = new Zend_Config($config);
        }
        
        return $this->_defaultConfigObject;
    }

    /**
     * Config
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * Set resource config
     *
     * @param Zend_Config $config
     * @return Zym_App_Resource_Abstract
     */
    public function setConfig(Zend_Config $config, $environment = null)
    {   
        if (!($this->_config = $this->getCache('__config'))) {
            // Merge default config with user config
            $defaultConfig = $this->getDefaultConfig($environment);
            $this->_config = $this->_mergeConfig($defaultConfig, $config);
        
            $this->saveCache($this->_config);
        }
        
        return $this;
    }

    /**
     * Get Application
     *
     * @return Zym_App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * Set Application
     *
     * @param Zym_App $application
     * @return Zym_App_Resource_Abstract
     */
    public function setApp(Zym_App $application)
    {
        $this->_app = $application;
        return $this;
    }
    
    /**
     * Get resource dispatch priority
     * 
     * @return integer
     */
    public function getPriority() 
    { 
        return $this->_priority; 
    }

    /**
     * Set a priority to use when determining dispatch order
     * 
     * You may use any integers for fine grained control as constants are provided
     * for simplicity.
     * 
     * @param integer $priority
     * @return Zym_App_Resource_Abastract
     */
    public function setPriority($priority)
    { 
        $this->_priority = (int) $priority;
        return $this;
    }
    
    /**
     * Get the internal bootstrap registry
     *
     * @param string $index Shortcut to $this->getRegistry()->get($index)
     * @param mixed $class Assert the class type of the get
     * @return Zym_App_Registry
     */
    public function getRegistry($index = null, $class = null)
    {
        $registry = $this->getApp()->getRegistry();
        if ($index !== null) {
            return $registry->get($index, $class);
        }

        return $registry;
    }
    
    /**
     * Get cache object
     *
     * @param string $id
     * @return Zend_Cache_Core|mixed
     */
    public function getCache($id = null)
    {
        if ($id !== null) {
            return $this->getApp()->getCache()->load($this->_makeCacheId($id));
        }
        
        return $this->getApp()->getCache();
    }
    
    /**
     * Save cache proxy
     *
     * @param mixed $value
     * @param string $id
     * @return boolean
     */
    public function saveCache($value, $id = null)
    {
        return $this->getApp()->getCache()->save($value, $this->_makeCacheId($id));
    }
    
    /**
     * Make cache id
     *
     * @param string $id
     * @return string
     */
    protected function _makeCacheId($id)
    {
        if ($id == null) {
            return null;
        }
        
        return get_class($this) . '__' . $this->getApp()->getEnvironment() . '__' . $id;
    }
    
    /**
     * Merge two arrays recursively, overwriting keys of the same name name
     * in $array1 with the value in $array2.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function _arrayMergeRecursiveOverwrite($array1, $array2)
    {
        if (is_array($array1) && is_array($array2)) {
            foreach ($array2 as $key => $value) {
                if (isset($array1[$key])) {
                    $array1[$key] = $this->_arrayMergeRecursiveOverwrite($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        } else {
            if (is_array($array1) && trim($array2) === '') {
                return $array1;
            }
            
            $array1 = $array2;
        }
        
        return $array1;
    }
    
    /**
     * A simple way to merge config sections and get a Zend_Config object
     *
     * @param Zend_Config|array $configA
     * @param Zend_Config|array $configB
     * @return Zend_Config
     */
    protected function _mergeConfig($configA, $configB)
    {
        /* We can't do this because there is no way to make sure it's a writable config obj
        // Use Zend_Config's merge
        if ($configA instanceof Zend_Config && $configB instanceof Zend_Config) {
            $configA->merge($configB);
            return $configA;
        }
        */
        
        // Convert to array
        $configA = ($configA instanceof Zend_Config) ? $configA->toArray() : (array) $configA;
        $configB = ($configB instanceof Zend_Config) ? $configB->toArray() : (array) $configB;
        
        $newConfig = $this->_arrayMergeRecursiveOverwrite($configA, $configB);
        return new Zend_Config($newConfig);
    }
    
    /**
     * Convert underscores to camel case
     * 
     * @param string $string
     * @return string
     */
    protected function _underscoreToCamelCase($string)
    {
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return $string;
    }
    
    /**
     * Destruct
     *
     */
    public function __destruct()
    {
        // Free circular reference
        unset($this->_app);
    }
}