<?php
/**
 * Jimw_Admin_Element_Abstract
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 
abstract class Jimw_Admin_Element_Abstract extends Jimw_Admin_Element_Base
{
    public $type;
    
    public function __toString() {
        return Zend_Json::encode($this);
    }
}