<?php
class Jimw_Admin_Form_TreeForm extends Jimw_Form
{
    public function __construct ($options = null)
    {
        parent::__construct($options);
        if (JIMW_DEBUG_MODE) {
            $this->addElements(array(
            'tree_lft' => array(
                'type' => 'text' ,
                'options' => array(
                	'label' => 'tree_lft')) ,
            'tree_rgt' => array(
                'type' => 'text' ,
                'options' => array(
                	'label' => 'tree_rgt')) ,
            'tree_creationdate' => array(
                'type' => 'text' ,
                'options' => array(
                	'label' => 'tree_creationdate')) ,
            'tree_editiondate' => array(
                'type' => 'text' ,
                'options' => array(
                	'label' => 'tree_creationdate')) ,
            'site_id' => array(
                'type' => 'Site' ,
                'options' => array(
                	'label' => 'site_id'))));
            $this->addDisplayGroup(array(
                'tree_lft' ,
                'tree_rgt' ,
                'tree_order' ,
                'tree_param' ,
                'tree_creationdate' ,
                'tree_editiondate' ,
                'site_id'), 'debug', array(
            	'legend' => 'Debug Params'));
        }
    }
    protected function initForm ()
    {
        $array = array(
        'tree_id' => array(
        	'type' => 'hidden') ,
        'tree_parentid' => array(
            'type' => 'Tree' ,
            'options' => array(
                'label' => 'Parent' ,
                'required' => true)) ,
        'module_path' => array(
            'type' => 'module' ,
            'options' => array(
                'label' => 'Module' ,
                'required' => true)) ,
        'tree_pagetitle' => array(
            'type' => 'text' ,
            'options' => array(
                'label' => 'Title' ,
                'required' => true,
                'onblur' => 'fillAlias(this.value, this.form)')) ,
        'tree_menutitle' => array(
            'type' => 'text' ,
            'options' => array(
                'label' => 'Menu Title' ,
                'required' => true,
                'onblur' => 'fillAlias(this.value, this.form)')) ,
        'tree_alias' => array(
            'type' => 'text' ,
            'options' => array(
                'label' => 'Alias' ,
                'required' => true ,
            	'validators' => array(
            		'Alias'),
                'description' => 'The alias is the url, use just alphanumeric charactere',
                'onblur' => 'fillAlias(this.value, this.form)',
                'onkeyup' => 'var v= cleanString(this.value); if (v != this.value) this.value = v;')) ,
        'tree_image' => array(
            'type' => 'imagechooser' ,
            'options' => array(
                'label' => 'Image')) ,
        'tree_description' => array(
            'type' => 'richtext' ,
            'options' => array(
                'label' => 'Description' ,
                'rows' => 3 ,
                'cols' => 40,
                'editor' => array('width' => 500, 'height' => 200, 'toolbarSet' => 'Basic'))) ,
        'tree_status' => array(
            'type' => 'select' ,
            'options' => array(
                'label' => 'Status' ,
                'multiOptions' => Jimw_Site_Tree::$STATUS_LIST)));
        return array(
        	'elements' => $array);
    }
}
?>