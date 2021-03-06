<?php
class BlogCommentForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array('elements' => array(
        'blogcomment_id' => 'hidden',
        'blogmessage_id' => 'hidden',
        'blogcomment_username' => array(
	        'type' => 'text' ,
	        'options' => array(
	        	'label' => 'Your name',
                'filters' => array('StringTrim', array('HtmlEntities', ENT_COMPAT, 'UTF-8')),
                'validators' => array(
                    array('Alnum', true),
                    array('StringLength', false, array(3, 20)),
                ),
	        	'required' => true)),
        'blogcomment_content' => array(
	        'type' => 'textarea' ,
	        'options' => array(
	        	'label' => 'Your message',
                'filters' => array('StringTrim', array('HtmlEntities', ENT_COMPAT, 'UTF-8')),
				'required' => true)),
        'capcha' => array('type' => 'captcha','options' => array( 
            'label'      => 'Please enter the letters displayed below:', 
            'required'   => true, 
            'captcha'    => array('captcha' => 'Figlet', 'wordLen' => 6, 'timeout' => 300, 'height' => 100, 'width' => 300, 'fontSize' => 48) 
            )),
        'submit' => array(
        	'type' => 'submit',
            'options' => array('required' => false, 'ignore' => true, 'label' => 'Send'))
        ));
    }
}
