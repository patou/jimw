<?php
class Jimw_Form_SubForm extends Zend_Form_SubForm
{
    public function __construct($config = null) {
        $this->addPrefixPath('Jimw_Form_Decorator', 'Jimw/Form/Decorator', 'decorator')
            ->addPrefixPath('Jimw_Form_Element', 'Jimw/Form/Element', 'element')
            ->addElementPrefixPath('Zym_Validate', 'Zym/Validate', 'validate')
            ->addElementPrefixPath('Zym_Filter', 'Zym/Filter', 'filter')
            ->addElementPrefixPath('Jimw_Form_Decorator', 'Jimw/Form/Decorator', 'decorator')
            ->addElementPrefixPath('Jimw_Form_Validate', 'Jimw/Form/Validate', 'validate')
            ->addElementPrefixPath('Jimw_Form_Filter', 'Jimw/Form/Filter', 'filter')
            ->addDisplayGroupPrefixPath('Jimw_Form_Decorator', 'Jimw/Form/Decorator');
        $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('Fieldset');
        parent::__construct($config);
    }
}
?>