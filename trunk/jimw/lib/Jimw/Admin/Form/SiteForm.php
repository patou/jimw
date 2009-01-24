<?php
class Jimw_Admin_Form_SiteForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array(
        'elements' => array(
	        'site_id' => array('type' => 'hidden') ,
	        'tree_id' => array(
		        'type' => 'Tree' ,
		        'options' => array(
			        'label' => 'Site Tree root' ,
			        'required' => true)) ,
	        'default_tree_id' => array(
		        'type' => 'Tree' ,
			        'options' => array(
			        'label' => 'Default page' ,
			        'required' => true ,
			        'allowRoot' => false)) ,
	        'domain_id' => array(
		        'type' => 'Domain' ,
		        'options' => array(
			        'label' => 'Domain' ,
			        'required' => true)) ,
	        'site_parentid' => array(
		        'type' => 'site' ,
		        'options' => array(
			        'label' => 'Parent Site' ,
			        'required' => true)) ,
	        'site_name' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Name' ,
			        'required' => true)) ,
	        'site_access' => array(
		        'type' => 'text' ,
		        'options' => array(
		        	'label' => 'Access')) ,
	        'site_path' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Path' ,
			        'required' => true)) ,
	        'site_template' => array(
		        'type' => 'text' ,
		        'options' => array(
		        	'label' => 'Template'))));
    }
}
?>