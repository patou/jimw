<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Loader
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Loader_Abstract
 */
require_once 'Zym/Loader/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Loader
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Loader_TableLoader extends Zym_Loader_Abstract
{
    /**
     * @var string
     */
    protected $_modelDirectory = 'tables';

    /**
     * @var Zym_Loader_TableLoader
     */
    protected static $_instance = null;

    /**
     * Singleton
     *
     * @return Zym_Loader_TableLoader
     */
    protected static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Load the form
     * 
     * @param string $formName
     * @param string $module
     * @param string $modelPrefix
     */
    public static function load($formName, $module = null, $modelPrefix = 'Table')
    {
        $formLoader = self::getInstance();
        $formLoader->loadModel($formName, $module, $modelPrefix);
    }
}