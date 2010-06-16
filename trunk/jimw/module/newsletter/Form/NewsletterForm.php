<?php

require_once ('jimw\lib\Jimw\Form.php');

class newsletter_Form_NewsletterForm extends Jimw_Form {
    protected function initForm ()
    {
        return array(
		    	'elements' => array(
		            'email' => array(
		                'type' => 'Email'
		                ,'options' => array(
		                    'label' => 'Email'
		                    ,'required' => true
		                    )
		            )
		            ,'send' => array(
		                'type' => 'submit'
		                ,'options' => array(
		                    'label' => 'Send'
		                )
		            )
		        )
		    );
    }
}