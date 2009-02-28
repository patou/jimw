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
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_App_Registry
 */
require_once 'Zym/App/Registry.php';

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Bootstraping component
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App
{
    /**
     * Environment type development
     *
     */
    const ENV_DEVELOPMENT = 'development';

    /**
     * Environment type production
     *
     */
    const ENV_PRODUCTION  = 'production';

    /**
     * Environment type test
     *
     */
    const ENV_TEST        = 'test';

    /**
     * Environment type default
     */
    const ENV_DEFAULT     = 'default';

    /**
     * Config directory
     *
     */
    const PATH_CONFIG = 'config';

    /**
     * Temp directory
     *
     */
    const PATH_TEMP   = 'temp';

    /**
     * Web directory
     *
     */
    const PATH_WEB    = 'web';

    /**
     * Application directory
     *
     */
    const PATH_APP    = 'app';

    /**
     * Data directory
     *
     */
    const PATH_DATA   = 'data';

    /**
     * Tests directory
     *
     */
    const PATH_TESTS  = 'tests';


    /**
     * Instance
     *
     * @var Zym_App
     */
    protected static $_instance;

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected $_cache;

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
        self::ENV_PRODUCTION  => array(
            'cache' => array(
                'enabled' => true
            )
        ),

        self::ENV_DEVELOPMENT => array(),

        self::ENV_TEST        => array(),

        self::ENV_DEFAULT     => array(
            'name' => 'App',

            'home' => '../',

            'namespace' => array(
                'Zym' => 'Zym_App_Resource'
            ),

            'path' => array(
                self::PATH_APP    => 'app',
                self::PATH_CONFIG => 'config',
                self::PATH_DATA   => 'data',
                self::PATH_TEMP   => 'temp',
            ),

            'default_resource' => array(
                'disabled'    => false,
                'config'      => '%s.xml',
                'environment' => null,
                'namespace'   => null,
                'priority'    => null
            ),

            'cache' => array(
                'enabled' => false,
                'prefix'  => '%s__'
            ),

            'resource' => array(),
            'registry' => 'Zym_App_Registry'
        )
    );

    /**
     * Default config object cache
     *
     * @var Zend_Config
     */
    private $_defaultConfigObject;

    /**
     * Environment
     *
     * @var string
     */
    protected $_environment = self::ENV_PRODUCTION;

    /**
     * Force default priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Internal Registry instance
     *
     * @var Zym_App_Registry
     */
    protected $_registry;

    /**
     * Array of resource instances
     *
     * @var array
     */
    protected $_resources = array();

    /**
     * Array of resource script paths
     *
     * @var array
     */
    protected $_scriptPaths = array();

    /**
     * Construct
     *
     * Protected to prevent instantiation
     */
    protected function __construct()
    {}

    /**
     * Clone
     *
     * Enforce singleton
     */
    protected function __clone()
    {}

    /**
     * Get the application instance
     *
     * @return Zym_App
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Config
     *
     * @param Zend_Config|string
     * @return Zym_App
     */
    public function setConfig($config, $format = null)
    {
        $environment = $this->getEnvironment();

        if (!$config instanceof Zend_Config) {
            $configObj   = $this->_loadConfig($config, $environment, $format);
        } else {
            $configObj = $config;
        }

        // Merge default config with user config
        $defaultConfig = $this->getDefaultConfig($environment);
        $this->_config = $this->_mergeConfig($defaultConfig, $configObj);

        return $this;
    }

    /**
     * Get Config
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        if (!$this->_config instanceof Zend_Config) {
            $this->setConfig($this->getDefaultConfig());
        }

        return $this->_config;
    }

    /**
     * Return a Zend_Config object populated with appropriate properties and
     * reasonable default values for this resource type.
     *
     * @param  string $environment
     * @return Zend_Config
     */
    public function getDefaultConfig($environment = null)
    {
        // Cache config obj
        if (!$this->_defaultConfigObject instanceof Zend_Config) {
            // Config array
            $config = array();

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
     * Get path
     *
     * @param string $key
     * @param string $append
     * @return Zend_Config
     */
    public function getPath($key, $append = null)
    {
        // Return root instead
        $relativeRoot = substr($append, 0, 1);
        if (in_array($relativeRoot, array('/', '\\'))) { // We don't support windows specific format
            return $append;                              // Use '/' as it works on all platforms
        }

        $config = $this->getConfig();

        if (isset($config->path->{$key})) {
            $path = $this->getHome(self::_normalizePath($config->get('path')->get($key)));
        } else {
            /**
             * @see Zym_App_Exception
             */
            require_once 'Zym/App/Exception.php';
            throw new Zym_App_Exception(sprintf('Path "%s" does not exist.', $key));
        }

        // Append for relative paths
        if (!empty($append)) {
            $path .= $append;
        }

        return $path;
    }

    /**
     * Get home directory
     *
     * The home directory is the current working directory for this bootstrap
     * class. By referencing from the home, it allows this component to be
     * CLI friendly.
     *
     * Providing append allows it to append to the home path another path.
     * If the appending path is absolute, it will return the path instead.
     *
     * @param string $append
     * @return string
     */
    public function getHome($append = null)
    {
        // Return root instead
        $relativeRoot = substr((string) $append, 0, 1);
        if (in_array($relativeRoot, array('/', '\\'))) { // We don't support windows specific format
            return $append;                              // Use '/' as it works on all platforms
        }

        $config = $this->getConfig();

        if (!isset($config->home)) {
            /**
             * @see Zym_App_Exception
             */
            require_once 'Zym/App/Exception.php';
            throw new Zym_App_Exception('Config key "home" is not set');
        }

        $home = self::_normalizePath($config->get('home'));

        // Append home for relative paths
        if (!empty($append)) {
            $home .= $append;
        }

        return $home;
    }

    /**
     * Get name
     *
     * @param boolean $asId
     * @return string
     */
    public function getName($asId = false)
    {
        $name = $this->getConfig()->get('name');

        if ($asId === true) {
            $name = str_replace(' ', '_', $name);

            // Allow only Alnum
            $pattern = '/[^a-zA-Z0-9_]/';
            $name = preg_replace($pattern, '', (string) $name);
        }

        return (string) $name;
    }

    /**
     * Set cache object
     *
     * @param Zend_Cache_Core $cache
     */
    public function setCache(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Get cache object
     *
     * @param string $id
     * @return Zend_Cache_Core|mixed
     */
    public function getCache($id = null)
    {
        // Sanity check
        if (!$this->_cache instanceof Zend_Cache_Core) {
            /**
             * @see Zym_App_Exception
             */
            require_once('Zym/App/Exception.php');
            throw new Zym_App_Exception('Cache object has not been set.');
        }

        if ($id !== null) {
            return $this->_cache->load($this->_makeCacheId($id));
        }

        return $this->_cache;
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
        return $this->getCache()->save($value, $this->_makeCacheId($id));
    }

    /**
     * Add a script path to the stack
     *
     * @param string $path
     * @param string $prefix
     * @return Zym_App
     */
    public function addResourcePath($path, $prefix = 'Zym_App_Resource', $id = null)
    {
        // Make sure it ends in a DIRECTORY_SEPARATOR
        if (substr($path, -1, 1) != '/\\') {
            $path .= DIRECTORY_SEPARATOR;
        }

        // Make sure it ends in a _
        $prefix = rtrim($prefix, '_') . '_';

        $info['dir']    = $path;
        $info['prefix'] = $prefix;

        if ($id !== null) {
            $this->_scriptPaths[strtolower($id)] = $info;
        } else {
            $this->_scriptPaths[] = $info;
        }

        return $this;
    }

    /**
     * Add repository of init scripts by prefix
     *
     * @param string $prefix
     * @return Zym_App
     */
    public function addResourcePrefix($prefix, $id)
    {
        $path = str_replace('_', DIRECTORY_SEPARATOR, $prefix);
        $this->addResourcePath($path, $prefix, $id);

        return $this;
    }

    /**
     * Append a resource script into the dispatch process
     *
     * @param Zym_App_Resource_Abstract $resource
     * @return Zym_App
     */
    public function appendResource(Zym_App_Resource_Abstract $resource, $name = null)
    {
        // Get the resource name (Zym_Foo -> Foo)
        if ($name === null) {
            $fullClassName = get_class($resource);

            if (strpos($fullClassName, '_') !== false) {
                $name = strrchr($fullClassName, '_');
                $name = ltrim($name, '_');
            } else {
                $name = $fullClassName;
            }
        }

        $this->_resources[$name] = $resource;

        return $this;
    }

    /**
     * Returns resource with the specified $name
     *
     * @param string $name  resource name
     * @return Zym_App_Resource_Abstract|null  returns null if not found
     * @throws Zym_App_Resource_Exception  if $name is invalid
     */
    public function getResource($name)
    {
        if (!is_string($name) || empty($name)) {
            require_once 'Zym/App/Resource/Exception.php';
            throw new Zym_App_Resource_Exception("Invalid resource name");
        }

        // first, check if the name is explictly given to a resource
        if (array_key_exists($name, $this->_resources)) {
            return $this->_resources[$name];
        }

        $name = strtolower($name);

        // loop resources
        foreach ($this->_resources as $key => $resource) {
            $className = strtolower(get_class($resource));

            // if $name contains an underscore, assume full class name is given
            if (strpos($name, '_') !== false && $name == $className) {
                return $resource;
            }

            // extract resource name
            $className = split('_', $className);
            if ($className[count($className) - 1] == $name) {
                return $resource;
            }
        }

        return null;
    }

    /**
     * Clear resource scripts
     *
     * @return Zym_App
     */
    public function clearResources()
    {
        $this->setResources(array());
        return $this;
    }

    /**
     * Clear and set init scripts
     *
     * @param array $scripts Array of Zym_App_Resource_Abstract
     * @return Zym_App
     */
    public function setResources(array $scripts)
    {
        foreach ($scripts as $script) {
            if (!$script instanceof Zym_App_Resource_Abstract) {
                /**
                 * @see Zym_App_Exception
                 */
                require_once('Zym/Application/Exception.php');
                throw new Zym_App_Exception(sprintf(
                    'The array of resource scripts provided has an invalid entry "%s".'
                    . 'It should consist only of Zym_App_Resource_Abstract instances',
                    get_class($script)
                ));
            }
        }

        $this->_resources = $scripts;
        return $this;
    }

    /**
     * Get array of init script instances
     *
     * @return array
     */
    public function getResources()
    {
        return $this->_resources;
    }

    /**
     * Get script paths
     *
     * @return array
     */
    public function getResourcePaths()
    {
        return $this->_scriptPaths;
    }

    /**
     * Set the internal Application registry
     *
     * @param Zym_App_Registry $registry
     */
    public function setRegistry(Zym_App_Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * Get the internal Application registry
     *
     * @param string $index Shortcut to $this->getRegistry()->get($index)
     * @param mixed  $class Assert the class type of the get
     * @return Zym_App_Registry
     */
    public function getRegistry($index = null, $class = null)
    {
        // Setup the registry
        if (!$this->_registry instanceof Zym_App_Registry) {
            $this->setRegistry(new Zym_App_Registry());
        }

        // Get shortcut
        if ($index !== null) {
            return $this->_registry->get($index, $class);
        }

        return $this->_registry;
    }

    /**
     * Set environment
     *
     * @param string $env
     * @return Zym_App
     */
    public function setEnvironment($env)
    {
        $this->_environment = (string) $env;
        return $this;
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Dispatch the boot process
     *
     * @return void
     */
    public function dispatch()
    {
        // Get config
        $config = $this->getConfig();

        // Registry setup
        $this->_setupRegistry($config);

        // Cache setup
        $this->_setupCache($config);

        // Load namespaces
        $this->_parseNamespaces($config);

        // Load resources
        $this->_parseResources($config);

        // Sort dispatch order
        $scripts = $this->getResources();
        usort($scripts, array($this, '_dispatchSort'));
        $this->setResources($scripts);

        // Init script dispatch loop
        foreach ($scripts as $resource) {
            // Dispatch
            $resource->dispatch();
        }
    }

    /**
     * Run application
     *
     * @param Zend_Config|string $config
     * @param string             $environment
     * @param string             $format Configuration format ("ini")
     */
    public static function run($config, $environment = null, $format = null)
    {
        $instance = self::getInstance();

        // Set environment
        if ($environment !== null) {
            $instance->setEnvironment($environment);
        }

        $instance->setConfig($config, $format)
                 ->dispatch();
    }

    /**
     * Parse the config for resources
     *
     * @param Zend_Config $config
     */
    protected function _parseResources(Zend_Config $config)
    {
        if (!$config->get('resource') instanceof Zend_Config) {
            return;
        }

        // Lets handle resources provided by config
        foreach ($config->get('resource') as $name => $rawResConfig) {
            if (!$resource = $this->getCache('resource_' . $name)) {
                // Get default resource config
                $defaultResConfig = $config->get('default_resource')->toArray();

                // Convert placeholder to filename
                if (is_string($defaultResConfig['config'])) {
                    $defaultResConfig['config'] = sprintf($defaultResConfig['config'], $name);
                }

                // Merge default config with actual config
                $resource = $this->_mergeConfig($defaultResConfig, $rawResConfig);

                $this->getCache()->save($resource);
            }

            // Run if enabled
            if ($resource->get('disabled') === '' || $resource->get('disabled')) {
                continue;
            }

            // Environment
            $environment = $resource->get('environment') ? $resource->get('environment') : $this->getEnvironment();
            $namespace   = $resource->get('namespace')   ? $resource->get('namespace')   : null;

            // Load resource config
            if (!$resConfig = $this->getCache('resourceConfig_' . $name)) {
                if (!$resource->get('config') instanceof Zend_Config) {
                    // Load a resource config from file specified
                    $resConfigFile = $this->getPath(self::PATH_CONFIG, $resource->get('config'));

                    // Make sure it exists
                    if (file_exists($resConfigFile)) {
                        // Create config obj
                        $resConfig = $this->_loadConfig($resConfigFile, $environment);
                    } else {
                        $resConfig = new Zend_Config(array());
                    }
                } else {
                    // Use the config provided
                    $resConfig = $resource->get('config');
                }

                $this->getCache()->save($resConfig);
            }

            // Load resource object
            $loadedScript = $this->_loadResource($name, $namespace);

            $script = new $loadedScript($this, $resConfig, $environment);

            // Set custom priority
            if (!empty($resource->priority)) {
                $script->setPriority($resource->get('priority'));
            }

            // Make sure that it's a valid script
            if (!$script instanceof Zym_App_Resource_Abstract) {
                /**
                 * @see Zym_App_Exception
                 */
                require_once 'Zym/App/Exception.php';
                throw new Zym_App_Exception(
                    "Resource script \"$name\" is not an instance of Zym_App_Resource_Abstract"
                );
            }

            // Add into dispatch stack
            $this->appendResource($script, $name);
        }
    }

    /**
     * Load an resource script in LIFO order
     *
     * @param string $name
     * @param string $namespace Force to use a specific namespace
     * @return Zym_App_Resource_Abstract
     */
    protected function _loadResource($name, $namespace = null)
    {
        $name = ucfirst($name);
        $file = $name . '.php';
        $scriptPaths = $this->getResourcePaths();

        // Handle custom namespaces
        if ($namespace !== null) {
            $ns = strtolower($namespace);
            if (!isset($scriptPaths[$ns])) {
                /**
                 * @see Zym_App_Exception
                 */
                require_once 'Zym/App/Exception.php';
                throw new Zym_App_Exception(
                    "Cannot use namespace '$namespace' for  script '$name' because it does not exist"
                );
            }

            $scriptPaths = array($scriptPaths[$ns]);
        }

        foreach (array_reverse($scriptPaths) as $info) {
            $dir    = $info['dir'];
            $prefix = $info['prefix'];
            $class  = $prefix . $name;

            if (class_exists($class, false)) {
                return $class;
            } else {
                include_once($dir . $file);

                if (class_exists($class, false)) {
                    return $class;
                }
            }
        }

        /**
         * @see Zym_App_Exception
         */
        require_once 'Zym/App/Exception.php';
        throw new Zym_App_Exception(
            'Application resource script by name "' . $name . '" not found.'
        );
    }

    /**
     * Load Namespaces
     *
     */
    protected function _parseNamespaces(Zend_Config $config)
    {
        // Load namespaces
        foreach ($config->get('namespace') as $id => $namespace) {
            if ($namespace instanceof Zend_Config) {
                $this->addResourcePath($namespace->get('path'), $namespace->get('prefix'), $id);
            } else {
                // Allow setting namespaces using keys
                if (empty($namespace)) {
                    $namespace = $id;
                }

                $this->addResourcePrefix($namespace, $id);
            }
        }
    }

    /**
     * Normalize init script name for lookups
     *
     * @param  string $name
     * @return string
     */
    protected static function _normalizeScriptName($name)
    {
        if (strpos($name, '_') !== false) {
            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        }

        return ucfirst($name);
    }

    /**
     * Normalize a path
     *
     * Trims and removes and leading /\ and adds /
     *
     * @param string $path
     * @return string
     */
    protected static function _normalizePath($path)
    {
        return rtrim(trim($path), '/\\') . '/';
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
     * Sort function used by (@see run())
     *
     * @param Zym_App_Resource_Abstract $scriptA
     * @param Zym_App_Resource_Abstract $scriptB
     * @return integer
     */
    protected function _dispatchSort(Zym_App_Resource_Abstract $scriptA, Zym_App_Resource_Abstract $scriptB)
    {
        // Get priority
        $a = $scriptA->getPriority();
        $b = $scriptB->getPriority();

        // If priority is the same then umm... you know, put it above
        if ($a == $b) {
            return 1;
        }

        return ($a < $b) ? -1 : 1;
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
     * Load config file
     *
     * @param string $config
     * @param string $format
     * @return Zend_Config
     */
    protected function _loadConfig($config, $environment, $format = null)
    {
        // Find format
        if ($format === null) {
            $format = pathinfo($config, PATHINFO_EXTENSION);
        }

        $configClass = 'Zend_Config_' . ucfirst(strtolower($format));
        Zend_Loader::loadClass($configClass);

        $configObj = new $configClass($config, $environment);

        return $configObj;
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

        return get_class($this) . '__' . $this->getEnvironment() .'__' . $id;
    }

    /**
     * Setup registry
     *
     * @param Zend_Config $config
     */
    protected function _setupRegistry(Zend_Config $config)
    {
        $class = $config->get('registry');

        if ($class instanceof Zym_App_Registry) {
            $registry = $class;
        } else {
            Zend_Loader::loadClass($class);
            $registry = new $class();
        }

        $this->setRegistry($registry);
    }

    /**
     * Setup Cache
     *
     * @param Zend_Config $config
     */
    protected function _setupCache(Zend_Config $config)
    {
        // Disable cache
        if (!$config->get('cache')->get('enabled') || !extension_loaded('apc')) {
            if ($this->_cache instanceof Zend_Cache_Core) {
                $this->_cache->setOption('caching', false);

            } else {
                $this->_cache = Zend_Cache::factory('Core', 'File', array('caching' => false));
            }

            return;
        }

        if (!extension_loaded('apc')) {
            /**
             * @see Zym_App_Exception
             */
            require_once 'Zym/App/Exception.php';
            throw new Zym_App_Exception(
                'Extension "Apc" is required to use "' . get_class($this). '"\'s cache feature. '
                . 'Disable caching or set your own cache object.'
            );
        }

        $prefix = sprintf($config->get('cache')->get('prefix'), $this->getName(true));

        $this->_cache = Zend_Cache::factory('Core', 'Apc', array(
            'automatic_serialization' => true,
            'cache_id_prefix' => $prefix
        ));

    }
}