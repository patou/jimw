<?php
class MessageForm extends Jimw_Form
{
    protected function initForm ()
    {
        return array('elements' => array(
        'blogmessage_id' => 'hidden',
        'tree_parentid' => 'hidden',
        'blogmessage_date' => array(
	        'type' => 'text' ,
	        'options' => array(
	        	'label' => 'Date',
	        	'description' => 'Enter the date with the format YYYY-MM-DD hh:mm:ss'
        )),
        'blogmessage_title' => array(
	        'type' => 'text' ,
	        'options' => array(
	        	'label' => 'Title',
                'filters' => array('StringTrim', 'HtmlEntities'),
				'required' => true)),
        'tree_id' => array(
	        'type' => 'tree' ,
	        'options' => array(
	        	'label' => 'Read more',
	        	'description' => 'If you want to do a link to an another page on your website'
                )),
        'blogmessage_header' => array(
	        'type' => 'richtext' ,
	        'options' => array(
	        	'label' => 'Header',
	        	'description' => 'If you specified an header, it will be display on the first page without the content'
                )),
        'blogmessage_content' => array(
	        'type' => 'richtext' ,
	        'options' => array(
	        	'label' => 'Content',
                'required' => true
                )),
        'blogmessage_comment' => array(
	        'type' => 'checkbox' ,
	        'options' => array(
	        	'label' => 'Active comment'
            ))
        ));

    }
}
?>