<?php
/**
 * Jimw_Site_Plugins_GlobalRender
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Plugins_GlobalRender extends Zend_Controller_Plugin_Abstract
{
	private $view;
	
	private function createMenu() {
		/* @var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', '*');
		$select->from('jimw_domain', '*');
		$select->where('jimw_tree.site_id = jimw_domain.site_id');
		//$select->joinNatural('jimw_domain', '*');
		$select->order('tree_order ASC');
		$result = $db->fetchAll($select);
		return $result;
	}
	
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $this->view = new Zend_View ();
        $tree = $request->getTree ();
        $this->view->path = $request->getBaseUrl() . '/' . trim($request->site_path, '/') . '/template';
        $this->view->request = $request;
		$this->view->tree = $tree;
		$this->view->menu = $this->createMenu();
		$this->view->setScriptPath(trim($request->site_path, '/') . '/template');
        $response->prepend('header', $this->view->render('header.phtml'));
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $response->append('footer', $this->view->render('footer.phtml'));
    }
}
?>