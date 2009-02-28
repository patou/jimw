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
 * @package    Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Controller_Plugin_LayoutSwitcher_Abstract
 */
require_once 'Zym/Controller/Plugin/LayoutSwitcher/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Controller_Plugin_LayoutSwitcher_Module extends Zym_Controller_Plugin_LayoutSwitcher_Abstract
{
    /**
     * Switch layout depending on the current route
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_switchLayout($request->getModuleName());
    }
}