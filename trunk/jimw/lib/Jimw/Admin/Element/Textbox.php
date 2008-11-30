<?php
/**
 * Jimw_Admin_Element_Textbox
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Admin_Element_Textbox extends Jimw_Admin_Element_Abstract
{
    public $type = 'textbox';
    public $label = '';
    public $name = '';
    public $value = '';
    public function __construct ($label, $name, $value)
    {
        $this->setLabel($label);
        $this->setName($name);
        $this->setValue($value);
    }
    public function setLabel ($label)
    {
        $this->label = $this->urlEncode($label);
    }
    public function setName ($name)
    {
        $this->name = $this->urlEncode($name);
    }
    public function setValue ($value)
    {
        $this->value = $this->urlEncode($value);
    }
}