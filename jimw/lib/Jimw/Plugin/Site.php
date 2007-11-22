<?php


//class Jimw_Plugin_Site extends Zend_Controller_Plugin_Abstract {
    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
//    public function routeShutdown(Zend_Controller_Request_Abstract $request)
//    {
//    	$alias = $request->getPageAlias ();
//    	if (!empty($alias)) {
//			$tree = new Jimw_Site_Tree ();
//			$result = $tree->findByAlias($alias);
//			if (!$result) {
//				return false;
//			}
//			Jimw_Site_Tree::setCurrent($result->id);
//			$request->setTree($result);
//			$request->setParam('site_path', $result->site->path);
//    	}
//    	else {
//    		$request = $this->_request;
//			/* @var $request Jimw_Global_Request */
//			$domain = new Jimw_Site_Domain ();
//			$result = $domain->fetchRow(array ('path_name = ?' => $request->getDomainName(),
//							'path_port = ?' => $request->getDomainPort(),
//							'path_protocol = ?' => $request->getDomainProtocol(),
//							'path_subdomain = ?' => $request->getSubDomain(),
//							'path_path = ?' => $request->getPath()));
//			if (!$result) {
//				throw new Jimw_Exception("This ".$request->getPath()." url didn't exist");
//			}
//			$site = $result->findParentJimw_Site_Site();
//			$treeFactory = new Jimw_Site_Tree();
//			$tree = $treeFactory->findBySite($site);
//			//Zend_Debug::dump($tree);
//			if (!$result) {
//				throw new Jimw_Exception("This $site->tree_id page didn't exist");
//			}
//			$request->setTree($tree);
//			$request->setPageAlias($tree->alias);
//			$module = $tree->module->path;
//			$request->setParam('site_path', $tree->site->path);
//			//$this->_forward($tree->alias, $module, $module);
//	    		
//    	}
//    }

//}

?>
