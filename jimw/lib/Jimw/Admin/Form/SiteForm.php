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
	        'domain' => array(
		        'type' => 'text' ,
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
	        'site_path' => array(
		        'type' => 'text' ,
		        'options' => array(
			        'label' => 'Path' ,
			        'required' => true,
                    'value' => ltrim(JIMW_URL_PUBLIC_PATH, '/').'/',
                    'validators' => array(
            			 new Jimw_Form_Validate_Directory(JIMW_ROOT, true)))) ,
	        'site_template' => array(
		        'type' => 'Template' ,
		        'options' => array(
		        	'label' => 'Template',
                    'validators' => array(
            			new Jimw_Form_Validate_Directory(JIMW_ROOT, true))
        ))));
    }
}
?>