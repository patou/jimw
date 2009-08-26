<?php
class Jimw_Install_SiteCreateForm extends Zend_Form
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
        $database->setLegend('The database of your siteword');
        $this->addSubForm($database, 'database');
        //----------------------------------- Other options
        $option_url = new Zend_Form_Element_Text('url');
        $option_url->setLabel('Url');
        $this->addElement($option_url, 'url');
        $option_title = new Zend_Form_Element_Text('title');
        $option_title->setLabel('Title');
        $this->addElement($option_title, 'title');
        $option_path = new Zend_Form_Element_Text('path');
        $option_path->setLabel('Path');
        $option_path->setDescription('Enter the public repository for this website. If the directory didn\'t exist, it will be created');
        $option_path->setValue(ltrim(JIMW_URL_PUBLIC_PATH, '/'));
        $option_path->addValidator(new Jimw_Form_Validate_Directory(JIMW_ROOT, true));
        $this->addElement($option_path, 'path');
        $option_template = new Zend_Form_Element_Select('template');
        $option_template->setLabel('Template');
        $option_template->setDescription('Enter the public repository for this website');
        $option_template->addValidator(new Jimw_Form_Validate_Directory(JIMW_ROOT, false));
        $templates = array();
        $dir_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(JIMW_REP_PUBLIC),RecursiveIteratorIterator::SELF_FIRST);
        foreach ($dir_iterator as $dir) {
            if ($dir->isDir() && file_exists($dir->getPathname() . '/layout.phtml')) {
                $path = trim(str_replace('\\', '/', $dir->getPathname()), '../');
                $templates[$path] = trim(str_replace('\\', '/', $dir->getPathname()), '../'); 
            }
        }
        $option_template->setMultiOptions($templates);
        $this->addElement($option_template, 'template');
        $this->addDisplayGroup(array('url' , 'title', 'path', 'template'), 'options', array('legend' => 'Other configuration'));
        //------------------------- Admin user configuration
        $user = new Zend_Form_SubForm();
        $user_login = new Zend_Form_Element_Text('login');
        $user_login->setLabel('Login')->setRequired(true);
        $user->addElement($user_login, 'login');
        $user_pass = new Zend_Form_Element_Password('password');
        $user_pass->setLabel('Password')->setRequired(true);
        $user->addElement($user_pass, 'password');
        $user_firstname = new Zend_Form_Element_Text('firstname');
        $user_firstname->setLabel('FirstName')->setRequired(true);
        $user->addElement($user_firstname, 'firstname');
        $user_lastname = new Zend_Form_Element_Text('lastname');
        $user_lastname->setLabel('LastName')->setRequired(true);
        $user->addElement($user_lastname, 'lastname');
        $user_mail = new Zend_Form_Element_Text('mail');
        $user_mail->setLabel('Email')->setRequired(true);
        $user->addElement($user_mail, 'mail');
        $user->setLegend('The administrator user');
        $this->addSubForm($user, 'user');
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send');
        $submit->setAttrib('class', 'input_submit');
        $this->addElement($submit);
    }
}
?>
