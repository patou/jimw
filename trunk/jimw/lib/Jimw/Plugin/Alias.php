<?php

/**
 *
 */
class Jimw_Plugin_Alias extends Zend_Controller_Plugin_Abstract {
	
    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    	$alias = $request->getPageAlias ();
    	if (!empty($alias)) {
			$tree = new Jimw_Site_Tree ();
			$result = $tree->findByAliasWithModuleAndSite($alias);
			if (!$result) {
				return false;
			}
			Jimw_Site_Tree::setCurrent($result->id);
			$request->setTree($result);
			$request->setParam('site_path', $result->site->path);
			$module = $result->module->path;
			$request->setControllerName($module);
			$request->setModuleName($module);
			$request->setActionName($alias);
    	}
    	else {
    		$request = $this->_request;
			/* @var $request Jimw_Global_Request */
			$domain = new Jimw_Site_Domain ();
			$result = $domain->fetchRow(array ('domain_name = ?' => $request->getDomainName(),
							'domain_port = ?' => $request->getDomainPort(),
							'domain_protocol = ?' => $request->getDomainProtocol(),
							'domain_subdomain = ?' => $request->getSubDomain(),
							'domain_path = ?' => $request->getPath()));
			if (!$result) {
				throw new Jimw_Exception("This ".$request->getPath()." url didn't exist");
			}
			$site = $result->findParentJimw_Site_Site();
			$treeFactory = new Jimw_Site_Tree();
			$tree = $treeFactory->findBySite($site);
			if (!$tree) {
				throw new Jimw_Exception("This $site->tree_id page didn't exist");
			}
			Jimw_Site_Tree::setCurrent($tree->id);
			$request->setTree($tree);
			$request->setPageAlias($tree->alias);
			$module = $tree->module->path;
			$request->setParam('site_path', $tree->site->path);
			$request->setControllerName($module);
			$request->setModuleName($module);
			$request->setActionName($alias);
			//$this->_forward($tree->alias, $module, $module);
	    		
    	}
    }
	}

?>
