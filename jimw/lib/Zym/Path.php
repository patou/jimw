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
 * @package    Zym_Path
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Path
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Path
{
    /**
     * Directories inside the modules that need to be included
     *
     * @var array
     */
    protected $_dirs = array();

    /**
     * Constructor
     *
     * @param string|array $dirs
     */
    public function __construct($dirs = null)
    {
        if (!$dirs) {
            $dirs = array('models');
        }

        $this->_dirs = (array) $dirs;
    }

    /**
     * Add an application path.
     *
     * @param string $path
     * @return Zym_Path
     */
    public function addApplicationPath($path)
    {
        $directoryIterator = new DirectoryIterator($path);
        $ingoreDirs = array('models', 'views', 'controllers');

        $dirs = array();

        foreach ($directoryIterator as $file) {
            $filename = $file->getFilename();

            if ($file->isDir() && !$file->isDot() && strpos($filename, '.') !== 0
                && !in_array($filename, $ingoreDirs)) {
                $path = $file->getPathname();

                foreach ($this->_dirs as $directory) {
                    $directory = $path . '/' . $directory;

                    if (file_exists($directory)) {
                        $dirs[] = $directory;
                    }
                }
            }
        }

        if (!empty($dirs)) {
            $this->addDirectory(implode(PATH_SEPARATOR, $dirs));
        }

        return $this;
    }

    /**
     * Add a directory to the include path
     *
     * @param string $directory
     * @return Zym_Path
     */
    public function addDirectory($directory)
    {
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $directory);

        return $this;
    }
}