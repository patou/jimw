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
				throw new Jimw_Site_Exception_AliasNotFound($alias);
			}
			Jimw_Site_Tree::setCurrent($result->id);
			$request->setTree($result);
			$request->setParam('site_path', $result->site->path);
			$module = $result->module->path;
			if ($request->getModuleName() == 'default')
				$request->setModuleName($module);
			if ($request->getControllerName() == 'index')
				$request->setControllerName($module);
			if ($request->getActionName() == 'index')
				$request->setActionName($alias);
    	}
	}
}
