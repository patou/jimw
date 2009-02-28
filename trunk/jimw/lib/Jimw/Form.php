<?php
/**
 * Jimw_Form
 * Extend the Zend_Form
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Form.php 157 2008-02-07 13:19:58Z patou.de.saint.steban $
 */
class Jimw_Form extends Zend_Form
{

    public function __construct ($options = null)
    {
        //->addPrefixPath('Zym_Validate', 'Zym/Validate', 'validate')
        //->addPrefixPath('Zym_Filter', 'Zym/Filter', 'filter')
        //->addPrefixPath('Jimw_Form_Validate', 'Jimw/Form/Validate', 'validate')
        //->addPrefixPath('Jimw_Form_Filter', 'Jimw/Form/Filter', 'filter')
        $form = $this->initForm();
        if ($options instanceof Zend_Config)
        {
            $options = $options->toArray();
        }
        if (is_array($options)) {
            $form = array_merge_recursive($form, $options);
        }

        $this->addPrefixPath('Jimw_Form_Decorator', 'Jimw/Form/Decorator', 'decorator')
            ->addPrefixPath('Jimw_Form_Element', 'Jimw/Form/Element', 'element')
            ->addElementPrefixPath('Zym_Validate', 'Zym/Validate', 'validate')
            ->addElementPrefixPath('Zym_Filter', 'Zym/Filter', 'filter')
            ->addElementPrefixPath('Jimw_Form_Decorator', 'Jimw/Form/Decorator', 'decorator')
            ->addElementPrefixPath('Jimw_Form_Validate', 'Jimw/Form/Validate', 'validate')
            ->addElementPrefixPath('Jimw_Form_Filter', 'Jimw/Form/Filter', 'filter')
            ->addDisplayGroupPrefixPath('Jimw_Form_Decorator', 'Jimw/Form/Decorator')
            ->setMethod('POST');
        parent::__construct($form);
    }

    /**
     * Overide this function to create the form
     * @return array The array form of the created form
     */
    protected function initForm()
    {
        return array();
    }

    public function addSubmit ($type = 'submit')
    {
        $this->addElement('submit', 'Jimw_Form_Submit', array('label' => ucfirst($type), 'ignore' => true));
    }
}
?>
