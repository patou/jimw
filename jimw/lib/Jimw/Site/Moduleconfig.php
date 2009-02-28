<?php
/**
 * Jimw_Site_Module
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Module.php 253 2008-11-30 22:34:54Z patou.de.saint.steban $
 */
class Jimw_Site_Moduleconfig extends Jimw_Db_Table
{
    protected $_name = 'moduleconfig';
    protected $_dependentTables = array('Jimw_Site_Site', 'Jimw_Site_Module');
    protected $_paramsField = array('config');
}