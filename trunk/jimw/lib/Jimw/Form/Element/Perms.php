<?php

class Jimw_Form_Element_Perms extends Zend_Form_Element_Radio
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formPerms';

    public function init() {
        parent::init();
        $options = array(Zend_Acl::TYPE_ALLOW => 'Allow', Zend_Acl::TYPE_DENY => 'Deny', NULL => 'Default');
        $this->setMultiOptions($options);
        $this->setSeparator("\n");
    }
}
?>