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
class IndexController extends Jimw_Module_Action
{
    public function indexAction ()
    {
        $request = $this->getRequest();
        $site = Zend_Registry::get('site');
        $treeFactory = new Jimw_Site_Tree();
        $tree = $treeFactory->findBySite($site);
        if (! $tree) {
            throw new Jimw_Site_Exception_SiteTreeNotFound($site->tree_id);
        }
        Jimw_Site_Tree::setCurrent($tree->id);
        $request->setTree($tree);
        $request->setPageAlias($tree->alias);
        $module = $tree->module_path;
        $request->setParam('site_path', $tree->site->path);
        Jimw_Debug::display('IndexController:' . $module . '/' . $tree->alias);
        $this->_forward($tree->alias, $module, $module);
    }
}
?>