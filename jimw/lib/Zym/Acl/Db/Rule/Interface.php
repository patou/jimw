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
 * @package    Zym_Acl
 * @subpackage Db_Rule
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Acl
 * @subpackage Db_Rule
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
interface Zym_Acl_Db_Rule_Interface
{
    /**
     * Get the roles attached to this rule
     *
     * @return array
     */
    public function getRoles();

    /**
     * Get the resources attached to this rule
     *
     * @return array
     */
    public function getResources();
}