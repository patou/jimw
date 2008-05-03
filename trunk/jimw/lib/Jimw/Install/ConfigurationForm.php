<?php


class Jimw_Install_ConfigurationForm extends Zend_Form {
    public function __construct($option = null) {
        parent::__construct($option);
        $this->initForm();
    }
    
    public function initForm() {
        $database_type = new Zend_Form_Element_Select('database[type]');
        $database_type->setLabel('Type of database')
                         ->setRequired(true)
                         ->setMultiOptions(array('PDO_MYSQL' => 'Mysql (PDO)', 'MYSQLI' => 'Mysql (Mysqli)', 'PDO_SQLITE' => 'Sqlite (PDO)'));                         
        $this->addElement($database_type);
        $database_dbname = new Zend_Form_Element_Text('database[dbname]');
        $database_dbname->setLabel('Name of database')
                         ->setRequired(true);                         
        $this->addElement($database_dbname);
        $database_host = new Zend_Form_Element_Text('database[host]');
        $database_host->setLabel('Hostname');                         
        $this->addElement($database_host);
        $database_username = new Zend_Form_Element_Text('database[username]');
        $database_username->setLabel('Username');  
        $this->addElement($database_username);
        $database_password = new Zend_Form_Element_Text('database[password]');
        $database_password->setLabel('Password');  
        $this->addElement($database_password);
        $this->addDisplayGroup(array($database_type, $database_dbname, $database_host, $database_username, $database_password), 'database');
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send');
        $submit->setAttrib('class', 'input_submit');
        
        $this->addElement($submit);
    }
}

?>
