<?php
class Jimw_Install_ConfigurationForm extends Jimw_Form
{
    public function __construct ($option = null)
    {
        parent::__construct($option);
        $this->initForm();
    }
    public function initForm ()
    {
        //------------------------------- Database form
        $database = new Zend_Form_SubForm();
        $database_type = new Zend_Form_Element_Select('type');
        $database_type->setLabel('Type of database')->setRequired(true)->setMultiOptions(array('PDO_MYSQL' => 'Mysql (PDO)' , 'MYSQLI' => 'Mysql (Mysqli)' , 'PDO_SQLITE' => 'Sqlite (PDO)'));
        $database->addElement($database_type, 'type');
        $database_dbname = new Zend_Form_Element_Text('dbname');
        $database_dbname->setLabel('Name of database')->setRequired(true);
        $database_dbname->setDescription("The database must exist");
        $database->addElement($database_dbname, 'dbname');
        $database_host = new Zend_Form_Element_Text('host');
        $database_host->setLabel('Hostname');
        $database->addElement($database_host, 'host');
        $database->addElement($database_dbname, 'dbname');
        $database_prefix = new Zend_Form_Element_Text('prefix');
        $database_prefix->setLabel('Table Prefix');
        $database->addElement($database_prefix, 'prefix');
        $database_username = new Zend_Form_Element_Text('username');
        $database_username->setLabel('Username');
        $database->addElement($database_username, 'username');
        $database_password = new Zend_Form_Element_Text('password');
        $database_password->setLabel('Password');
        $database->addElement($database_password, 'password');
        $database->setLegend('You must configure a global database');
        $this->addSubForm($database, 'database');
        //----------------------------------- Other options
        $options_lang = new Zend_Form_Element_Select('default_lang');
        $options_lang->setLabel('Default language')->setMultiOptions(array('fr' => 'French' , 'en' => 'English'));
        $this->addElement($options_lang, 'default_lang');
        $option_debug = new Zend_Form_Element_Checkbox('debug');
        $option_debug->setLabel('Debug mode');
        $this->addElement($option_debug, 'debug');
        $option_install = new Zend_Form_Element_Checkbox('install_protect');
        $option_install->setLabel('Use only this domain for access to this install administration (recommanded)');
        $this->addElement($option_install, 'install_protect');
        $option_rewrite = new Zend_Form_Element_Checkbox('rewrite');
        $option_rewrite->setLabel('Use rewriting mode');
        $this->addElement($option_rewrite, 'rewrite');
        $option_utf8 = new Zend_Form_Element_Checkbox('utf8');
        $option_utf8->setLabel('Use UTF8');
        $this->addElement($option_utf8, 'utf8');
        $akismet_key = new Zend_Form_Element_Text('akismet_key');
        $akismet_key->setLabel('Akismet Key');
        $this->addElement($akismet_key, 'akismet_key');
        $akismet_url = new Zend_Form_Element_Text('akismet_url');
        $akismet_url->setLabel('Akismet Url');
        $this->addElement($akismet_url, 'akismet_url');
        $this->addDisplayGroup(array('default_lang' , 'debug' , 'install_protect' , 'rewrite' , 'utf8' , 'akismet_key' , 'akismet_url'), 'options', array('legend' => 'Other configuration'));
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send');
        $submit->setAttrib('class', 'input_submit');
        $this->addElement($submit);
    }
}
?>
