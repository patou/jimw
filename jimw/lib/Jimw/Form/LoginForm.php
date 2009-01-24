<?php
class Jimw_Form_LoginForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array('elements' => array(
        'redirect' => 'hidden',
        'login' => array(
	        'type' => 'text' ,
	        'options' => array(
	        	'label' => 'Username',
                'filters' => array('StringTrim'),
                'validators' => array(
                    'Alpha',
                    array('StringLength', false, array(3, 20)),
                ),
	        	'required' => true)),
        'password' => array(
	        'type' => 'password' ,
	        'options' => array(
	        	'label' => 'Password',
                'filters' => array('StringTrim'),
				'validators' => array(
					'Alnum',
                    array('StringLength', false, array(3, 20)),
                ),
	        	'required' => true)),
        'submit' => array(
        	'type' => 'submit',
            'options' => array('required' => false,'ignore' => true,'label' => 'Send'))
        ));
    }

}
?>