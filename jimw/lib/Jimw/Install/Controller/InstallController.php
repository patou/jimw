<?php

/**
 * InstallController
 * 
 * @author
 * @version 
 */

class InstallController extends Jimw_Install_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->license = file_get_contents(JIMW_ROOT.'LICENSE');
	}

	public function configurationAction () {
	    global $jimw_config_db;
	    $form = new Jimw_Install_ConfigurationForm();
	    $req = $this->getRequest();
	    if ($req->isPost()) {
	        $database = $req->database;
	        $db_global = Zend_Db::factory($database['type'], $database);
	        if ($db_global->getConnection()) {
    	        
    	        $conf = '<?' . PHP_EOL;
    	        foreach ($req->database as $name => $value) {
    	            $conf .= '$jimw_config_db[\'' . $name . '\'] = \'' . $value . '\';' . PHP_EOL;
    	        } 
    	        if (isset($req->debug)) {
    	            $conf .= 'define(\'JIMW_DEBUG_MODE\', ' .($req->debug ? 'true' : 'false') . ');' . PHP_EOL;
    	        }
    	        if (isset($req->default_lang)) {
    	            $conf .= 'define(\'JIMW_LANG\', \'' . ($req->default_lang) . '\');' . PHP_EOL;
    	        }
    	        if (isset($req->rewrite)) {
    	            $conf .= 'define(\'JIMW_URL_REWRITING\', ' . ($req->rewrite ? 'true' : 'false') . ');' . PHP_EOL;
    	        }
    	        if (isset($req->install_protect)) {
    	            $conf .= 'define(\'JIMW_INSTALL_PROTECT_DOMAIN\', \'' . $req->getSubDomain() .'.'.$req->getDomainName() . '\');' . PHP_EOL;
    	        }
    	        $conf .= '?>' . PHP_EOL;
    	        file_put_contents(JIMW_REP_CONFIG . '/global.local.php', $conf);
    	        Jimw_Debug::display('<pre>' .htmlentities($conf) . '</pre>', 'Configuration');
    	        $this->_redirect('/update.php');
	        }
	        else {
	            $this->view->database = $jimw_config_db;
	            $form->populate($jimw_config_db);
	            $this->view->form = $form;
	            //$this->install_protect = defined('JIMW_INSTALL_PROTECT_DOMAIN');
	        }
	    }
	    else {
	        $this->view->database = $jimw_config_db;
	        $form->populate($jimw_config_db);
	        $this->view->form = $form;
	        $this->install_protect = defined('JIMW_INSTALL_PROTECT_DOMAIN');
	    }
	}
}
