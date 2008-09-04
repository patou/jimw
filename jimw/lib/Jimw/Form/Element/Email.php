<?php
class Jimw_Form_Element_Email extends Zend_Form_Element_Text {
    public function init() {
        $this->addValidator('emailAddress');
    }
}

?>
