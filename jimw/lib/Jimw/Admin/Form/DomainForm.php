<?php
class Jimw_Admin_Form_DomainForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array(
        'elements' => array(
	        'domain' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Domain' ,
			        'required' => true,
                    'value' => 'http://')) ,
	        'domain_catch_domain' => array(
		        'type' => 'checkbox' ,
		        'options' => array(
			        'label' => 'Catch all subdomain of the giver domain')) ,
	        'domain_catch_all' => array(
		        'type' => 'checkbox' ,
		        'options' => array(
			        'label' => 'Catch all domains')) ,
	        ));
    }
}
?>