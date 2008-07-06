<?php

/**
 * InstallController
 *
 * @author
 * @version
 */

class UpdateController extends Jimw_Install_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
	}

	public function svnAction() {
	    $result = array();
	    $return = 0;
	    exec('cd '. JIMW_ROOT . '& svn up', $result, $return);
	    $this->view->result = implode("\n<br />", $result);
	    $this->view->return = $return;
	}


	public function databaseAction () {
	    global $jimw_config_db;
	    $db_global = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);
	    $update = new Jimw_Db_Update($db_global, $jimw_config_db['prefix']);
	    ob_start();
	    $update->update_all();
	    $this->view->result = ob_get_clean();
	}
}
