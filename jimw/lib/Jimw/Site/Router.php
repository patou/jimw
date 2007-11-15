<?php
/**
 * Jimw_Site_Router
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Router extends Zend_Controller_Router_Rewrite
{
	/**
	 * Get the controller name
	 */
	private function getController($path, Jimw_Global_Request $request) {
		$tab = explode('.', $path);
		if (isset($tab[1]))
			$request->setParam('ext', $tab[1]);
		$tab_path = explode('_', $tab[0]);
		$nb = count($tab_path);
		for ($i = 1; $i < $nb; $i += 2) {
			$key = $tab_path[$i];
			if ($i + 1 < $nb)
				$value = $tab_path[$i + 1];
			else
				$value = true;
			$request->setParam($key, $value);
		}
		return $tab_path[0];
	}

	/**
	 * Obsolete
	 *
	 * @param Jimw_Global_Request $request
	 */
	private function getSite (Jimw_Global_Request $request) {
		$db = Zend_Registry::get('db');
        $select = $db->select ();
        $select->from('jimw_site', '*');
        $select->joinNatural('jimw_domain', 'site_id');
        $select->where('domain_name = ?', $request->getDomainName());
        $select->where('domain_port = ?', $request->getDomainPort());
        $select->where('domain_protocol = ?', $request->getDomainProtocol());
        $select->where('domain_subdomain = ?', $request->getSubDomain());
        $result = $db->fetchRow($select);
        $request->setParam('site_path', $result['site_path']);
	}

	private function getModuleAlias($alias, Jimw_Global_Request $request) {
		$tree = new Jimw_Site_Tree ();
		$result = $tree->findByAlias($alias);
		if (!$result) {
			return false;
		}
		Jimw_Site_Tree::setCurrent($result->id);
		$request->setTree($result);
		$request->setParam('site_path', $result->site->path);
		return $result->module->path;
	}

	private function _setPath($tab_path, $length, Jimw_Global_Request $request) {
		if ($length > 0) {
			$path = implode('/', array_slice($tab_path, 0, $length));
			$base = $request->getBasePath ();
			if (!empty($base))
				$path = $base . '/' . $path;
			$request->setPath(trim($path, '/'));
		}
	}

	/**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @param  Zend_Controller_Request_Abstract
     * @throws Jimw_Global_Exception
     * @return Zend_Controller_Request_Abstract|boolean
     */
//    public function route(Zend_Controller_Request_Abstract $request)
//    {
//    	if (!$request instanceof Jimw_Global_Request) {
//            throw new Zend_Controller_Router_Exception('Jimw_Global_Router requires a Jimw_Global_Request-based request object');
//     	}
//     	parent::route ($request);
//    }
//    public function route(Zend_Controller_Request_Abstract $request)
//    {
//    	if (!$request instanceof Jimw_Global_Request) {
//            throw new Zend_Controller_Router_Exception('Jimw_Global_Router requires a Jimw_Global_Request-based request object');
//        }
//        /* @var $request Jimw_Global_Request */
//    	// Just One module for the first version
//    	//echo $request->getBaseUrl () . '<br />';
//    	$path = $request->getPathInfo ();
//    	//echo $path . '<br />';
//    	$len_path = strlen($path);
//    	$tab_path = explode('/', trim($path, '/'));
//    	$len_tab_path = count($tab_path);
//    	if ($path[$len_path - 1] == '/') {
//    		$controller = false;
//    		if ($len_tab_path == 0)
//    		{
//    			$request->setModuleName('default');
//    			$request->setControllerName('index');
//    			$request->setActionName('index');
//    			$request->setPath(trim($request->getBaseUrl(), '/'));
//    		}
//    		else {
//    			$alias = $tab_path[$len_tab_path - 1];
//    			$this->_setPath($tab_path, $len_tab_path - 1, $request);
//    		}
//    	}
//    	else {
//       		if ($len_tab_path == 1 || strpos($tab_path[$len_tab_path - 1], '.') === false)
//    		{
//    			$alias = $tab_path[$len_tab_path - 1];
//    			$controller = false;
//    			$this->_setPath($tab_path, $len_tab_path - 1, $request);
//    		}
//    		else {
//    			$alias = $tab_path[$len_tab_path - 2];
//    			$controller = $this->getController($tab_path[$len_tab_path - 1], $request);
//    			$this->_setPath($tab_path, $len_tab_path - 2, $request);
//    		}
//    	}
//    	/*echo 'alias = ', $alias, '<br />';
//    	echo 'controler = ', $controller, '<br />';
//    	echo 'len_tab_path = ', $len_tab_path, '<br />';
//    	Zend_Debug::dump($tab_path);*/
//    	$module = $this->getModuleAlias($alias, $request);
//    	if ($module === false) {
//    		$request->setModuleName('default');
//    		$request->setControllerName('index');
//    		$request->setActionName('index');
//    		$this->_setPath($tab_path, $len_tab_path, $request);
//    	}
//    	else {
//	    	$request->setPageAlias($alias);
//	    	$request->setModuleName($module);
//	    	if ($controller === false)
//	    		$request->setControllerName($module);
//	    	else
//	    		$request->setControllerName($controller);
//	    	$request->setActionName($alias);
//    	}
//    	//$this->getSite($request);
//    	//Zend_Debug::dump($request);
//       	return $request;
//    }
    protected function _setRequestParams($request, $params)
    {
        foreach ($params as $param => $value) {
            $request->setParam($param, $value);
            
            if ($param === $request->getModuleKey()) {
                $request->setModuleName($value);
            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }
        	if ($param === $request->getAliasKey()) {
                $request->setPageAlias($value);
            }             
        }
    }
}
?>