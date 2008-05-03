<?php
class Jimw_Form_Element_Email extends Zend_Form_Element_Text {
    public function __construct($spec, $options = null) {
        parent::__construct($spec, $options);
        $this->addValidator('emailAddress');
    }
}

?>
