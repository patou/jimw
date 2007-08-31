<?php
/**
 * Jimw_Admin_Element
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:$
 */
 
class Jimw_Admin_Element extends Jimw_Admin_Element_Base 
{
    protected $elements = array();
    public function addLabel($value) {
        $this->elements[] = array('type' => 'label', 'value' => $this->urlEncode($value));
    }
    public function addAlert($message) {
        $this->elements[] = array('type' => 'alert', 'message' => $this->urlEncode($message));
    }
    public function addReloadTree($message) {
        $this->elements[] = array('type' => 'reloadtree');
    }
    public function addCloseTab($message) {
        $this->elements[] = array('type' => 'closetab');
    }
    public function addElement(Jimw_Admin_Element_Base $element) {
    	if ($element instanceof Jimw_Admin_Element) {
    		array_merge($this->elements, $element);
    	} else {
        	$this->elements[] = $element;
    	}
    }
    public function __toString() {
        return trim(Zend_Json::encode($this->elements), '[] ');
    }
}