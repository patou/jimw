<?php
/**
 * Jimw_Module_Action
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

abstract class Jimw_Module_Action_Abstract extends Zym_Controller_Action_Abstract
{
	public function init() {
		$request = $this->getRequest ();
		$tree = $request->getTree ();
		$site_path = trim($request->site_path, '/');
		$this->view->path = $request->getBaseUrl () . '/' . $site_path . '/template';
		$this->view->path_public = $request->getBaseUrl () . '/' . $site_path;
		$this->view->path_common = $request->getBaseUrl () . JIMW_URL_PUBLIC_COMMON_PATH;
		$ext = $request->getParam('format', 'phtml');
		Zend_Registry::set('format', $ext);
		$this->getHelper('ViewRenderer')->setViewSuffix($ext);
		$this->getHelper('Layout')->setViewSuffix($ext);
		$this->view->addScriptPath($site_path . '/template');
		$module = $request->getModuleName();
		$this->view->addScriptPath($site_path . '/template/' . $module);
		$this->view->addScriptPath($site_path);
		$this->view->title = '';
		if (!empty($tree)) {
			$this->view->title = $tree->pagetitle;
		}
		$this->view->request = $request;
		if (empty($tree)) {
			$trees = new Jimw_Site_Tree();
			$tree = $trees->fetchNew();
		}
		$this->view->tree = $tree;
		$this->initModule();
	}

	/**
	 * Initialisation of the module
	 * Call by init method
	 *
	 */

	public function initModule() {
	}

	/**
	 * Translate string
	 *
	 * @param string $messageId
	 * @param string $local
	 * @return string
	 */
	public function _($messageId, $local = null) {
		return $this->view->_($messageId, $local);
	}
}