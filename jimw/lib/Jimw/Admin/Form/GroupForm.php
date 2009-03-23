<?php
class Jimw_Admin_Form_GroupForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array(
        'elements' => array(
	        'group_id' => array('type' => 'hidden') ,
	        'group_name' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Name' ,
			        'required' => true)) ,
	        'group_parents' => array(
		        'type' => 'usergroup' ,
		        'options' => array(
		        	'label' => 'Parent',
                    'description' => 'Select parents groups'))
	        ));
    }
}
?>