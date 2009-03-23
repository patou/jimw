<?php
class Jimw_Admin_Form_UserForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array(
        'elements' => array(
	        'user_id' => array('type' => 'hidden') ,
	        'user_login' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Login' ,
			        'required' => true)) ,
	        'user_password' => array(
		        'type' => 'password' ,
			    'options' => array(
			        'label' => 'Password' ,
			        'required' => true ,
			        'allowRoot' => false)) ,
	        'user_firstname' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Firstname' ,
			        'required' => true)) ,
	        'user_lastname' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Lastname' ,
			        'required' => true)) ,
	        'user_email' => array(
		        'type' => 'email' ,
		        'options' => array(
			        'label' => 'Email' ,
			        'required' => true)),
            'usergroup' => array(
		        'type' => 'usergroup' ,
		        'options' => array(
			        'label' => 'Groups' ,
			        'required' => true))
        ));
    }
}
?>