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
		$request = $this->_request;
		/* @var $request Jimw_Global_Request */
		$domain = new Jimw_Site_Domain ();
		$result = $domain->fetchRow(array ('path_name = ?' => $request->getDomainName(),
						'path_port = ?' => $request->getDomainPort(),
						'path_protocol = ?' => $request->getDomainProtocol(),
						'path_subdomain = ?' => $request->getSubDomain(),
						'path_path = ?' => $request->getPath()));
		if (!$result) {
			throw new Jimw_Exception("This ".$request->getPath()." url didn't exist");
		}
		$site = $result->findParentJimw_Site_Site();
		$treeFactory = new Jimw_Site_Tree();
		$tree = $treeFactory->findBySite($site);
		//Zend_Debug::dump($tree);
		if (!$tree) {
			throw new Jimw_Exception("This $site->tree_id page didn't exist");
		}
		Jimw_Site_Tree::setCurrent($result->id);
		$request->setTree($tree);
		$request->setPageAlias($tree->alias);
		$module = $tree->module->path;
		$request->setParam('site_path', $tree->site->path);
		$this->_forward($tree->alias, $module, $module);
	}
}
?>