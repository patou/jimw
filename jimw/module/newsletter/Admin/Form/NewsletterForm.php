<?php

class newsletter_Admin_Form_NewsletterForm extends Jimw_Form {
    protected function initForm ()
    {
        return array(
		    	'elements' => array(
		            'title' => array(
		                'type' => 'text'
		                ,'options' => array(
		                    'label' => 'Title'
		                    ,'required' => true
		                    )
		            )
		            ,'message' => array(
		                'type' => 'richtext'
		                ,'options' => array(
		                    'label' => 'Message'
		                    ,'required' => true
		                    ,'rows' => 3
                			,'cols' => 40
                			,'editor' => array(
                				'width' => 800, 
                				'height' => 500, 
                				'toolbarSet' => 'Full')
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