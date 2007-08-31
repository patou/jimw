<?php
/**
 * Jimw_Admin_Element_Hidden
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:$
 */

class Jimw_Admin_Element_Hidden extends Jimw_Admin_Element_Abstract
{
    public $type = 'hidden';
    public $name = '';
    public $value = '';
    public function __construct($name, $value) {
        $this->setName($name);
        $this->setValue($value);
    }
    public function setName($name) {
        $this->name = $this->urlEncode($name);
    }
    public function setValue($value) {
        $this->value = $this->urlEncode($value);
    }
    public function addField($field) {
        $this->field[] = $field;
    }
}