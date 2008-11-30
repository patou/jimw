<?php
/**
 * Jimw_Admin_Element_Listbox
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Admin_Element_Listbox extends Jimw_Admin_Element_Abstract
{
    public $type = 'listbox';
    public $label = '';
    public $name = '';
    public $values = array();
    public function __construct ($label, $name)
    {
        $this->setLabel($label);
        $this->setName($name);
    }
    public function setLabel ($label)
    {
        $this->label = $this->urlEncode($label);
    }
    public function setName ($name)
    {
        $this->name = $this->urlEncode($name);
    }
    public function addValue ($value, $key = '')
    {
        if ($key == '') {
            $this->values[] = $this->urlEncode($value);
        } else {
            $this->values[urlEncode($key)] = urlEncode($value);
        }
    }
}