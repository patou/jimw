<?php
/**
 * IndexController
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 
class IndexController extends Zend_Controller_Action 
{
	public function index () {
		/* @var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db');
		/* @var $db Zend_Db_Adapter_Abstract */
		$select = $db->select();
		$select->from('jimw_tree', '*');
		$select->from('jimw_module', '*');
		$select->from('jimw_site', array('site_name', 'site_path'));
		$select->where('jimw_tree.tree_id = jimw_site.tree_id');
		$select->where('jimw_tree.module_id = jimw_module.module_id');		
		$result = $db->fetchRow($select);
		$this->_request->setTree($result);
		$this->_forward($result['tree_alias'], 'article', 'article');
	}
}
?>