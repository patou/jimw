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
	    if ($req->isPost() && $form->isValid($req->getPost())) {
	        $values = $form->getValues();
	        $database = $values['database'];
	        $db_global = Zend_Db::factory($database['type'], $database);
	        if ($db_global->getConnection()) {

    	        $conf = '<?' . PHP_EOL;
    	        foreach ($database as $name => $value) {
    	            $conf .= '$jimw_config_db[\'' . $name . '\'] = \'' . $value . '\';' . PHP_EOL;
    	        }
    	        if (isset($database['prefix'])) {
    	            $conf .= 'define(\'JIMW_PREFIX\', ' . $database['prefix'] . ');' . PHP_EOL;
    	        }
    	        if (isset($req->debug)) {
    	            $conf .= 'define(\'JIMW_DEBUG_MODE\', ' .($req->debug ? 'true' : 'false') . ');' . PHP_EOL;
    	        }
    	        if (isset($req->default_lang)) {
    	            $conf .= 'define(\'JIMW_LANG\', \'' . ($req->default_lang) . '\');' . PHP_EOL;
    	        }
    	        if (isset($req->rewrite)) {
    	            $conf .= 'define(\'JIMW_URL_REWRITING\', ' . ($req->rewrite ? 'true' : 'false') . ');' . PHP_EOL;
    	            $htaccess_dir = array(JIMW_ROOT, JIMW_ADMIN_ROOT, JIMW_REP_INSTALL, JIMW_REP_PUBLIC, JIMW_REP_ADMIN_PUBLIC);
    	            foreach ($htaccess_dir as $htaccess) {
    	                if (!file_exists($htaccess . '.htaccess') && file_exists($htaccess . '.htaccess.dist'))
    	                    @copy($htaccess . '.htaccess.dist', $htaccess . '.htaccess');
    	            }
    	        }
    	        if (isset($req->utf8)) {
    	            $conf .= 'define(\'JIMW_UTF8\', ' . ($req->utf8 ? 'true' : 'false') . ');' . PHP_EOL;
    	        }
    	        if (isset($req->install_protect)) {
    	            $conf .= 'define(\'JIMW_INSTALL_PROTECT_DOMAIN\', \'' . $req->getSubDomain() .'.'.$req->getDomainName() . '\');' . PHP_EOL;
    	        }
    	        $conf .= '?>' . PHP_EOL;
    	        file_put_contents(JIMW_REP_CONFIG . '/global.local.php', $conf);
    	        Jimw_Debug::display('<pre>' .htmlentities($conf) . '</pre>', 'Configuration');
    	        $update = new Jimw_Db_Update($db_global, $jimw_config_db['prefix']);
    	        ob_start();
    	        $update->update_all();
    	        $this->view->result = ob_get_clean();
    	        $this->render('success');
	        }
	    }
	    $form->populate($jimw_config_db);
	    $form->populate(array('debug' => defined('JIMW_DEBUG_MODE')
	                          ,'install_protect' => defined('JIMW_INSTALL_PROTECT_DOMAIN')
	                          ,'rewrite' => defined('JIMW_URL_REWRITING')
	                          ,'utf8' => defined('JIMW_UTF8')
	                          ,'default_lang' => defined('JIMW_LANG')));
	    $this->view->form = $form;
	}
}
