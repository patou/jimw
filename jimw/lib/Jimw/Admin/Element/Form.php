<?php
/**
 * Jimw_Admin_Element_Form
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:$
 */

class Jimw_Admin_Element_Form extends Jimw_Admin_Element_Abstract
{
    public $type = 'form';
    public $method = 'post';
    public $action = '';
    public $field = array();
    public function __construct($action) {
        $this->setAction($action);
    }
    public function setAction($action) {
        $this->action = $this->urlEncode($action);
    }
    public function setMethod($method) {
        $this->method = $this->urlEncode($method);
    }
    public function addField($field) {
        $this->field[] = $field;
    }
}