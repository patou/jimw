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
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Registers class autoloader
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Autoload extends Zym_App_Resource_Abstract
{   
    /**
     * Priority
     * 
     * Try to load this before any other ones
     *
     * @var integer
     */
    protected $_priority = self::PRIORITY_HIGH;
    
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'class' => 'Zend_Loader'
        )
    );

    /**
     * Setup
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {
        // Use non-default autoload function?
        $class = $config->get('class');
        
        // Allow loading multiple loaders
        if ($class instanceof Zend_Config) {
            $classes = $class->toArray();
        } else {
            $classes = (array) $class;
        }
        
        // Register autoload
        foreach (array_reverse($classes) as $loader) {
            Zend_Loader::registerAutoload($loader);
        }
    }
}