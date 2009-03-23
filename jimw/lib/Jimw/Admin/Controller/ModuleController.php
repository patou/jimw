<?php
/**
 * ModuleController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class ModuleController extends Jimw_Admin_Action
{
    public function preDispatch()
    {
        $this->checkRoleAllowed('admin_system');
    }
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $this->_forward('list');
    }
    public function listAction ()
    {
        $list = array();
        $modules = new Jimw_Site_Module();
        $modules_list = $modules->fetchAll();
        foreach ($modules_list as $module) {
            $list[$module->path] = array('path' => $module->path , 'id' => $module->id , 'name' => $module->name , 'version' => $module->version , 'author' => $module->author , 'comment' => $module->comment , 'activ' => true);
        }
        $dir = new DirectoryIterator(JIMW_REP_MODULE);
        if ($dir->valid()) {
            foreach ($dir as $module) {
                if (! $dir->isDot() && ! isset($list[$module->getFilename()]) && is_readable(JIMW_REP_MODULE . '/' . $module->getFilename() . '/jimw.xml')) {
                    $xml = simplexml_load_file(JIMW_REP_MODULE . '/' . $module . '/jimw.xml');
                    $list[$module->getFilename()] = array('path' => $module->getFilename() , 'name' => $xml->name , 'version' => $xml->version , 'author' => $xml->author , 'comment' => $xml->comment , 'activ' => false);
                }
            }
        }
        ksort($list);
        $this->view->modules_list = $list;
    }
    public function disableAction ()
    {
        $modules = new Jimw_Site_Module();
        $trees = new Jimw_Site_Tree();
        $module = $modules->find($this->_request->id)->current();
        $list = $trees->fetchAll($trees->select()->where('module_path = ?', $module->path));
        if (count($list) == 0)
            $module->delete();
        else {
            $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('This module is used by page, you must delete all page to desactivate this module'));
        }
        $this->_forward('list');
    }
    public function enableAction ()
    {
        $modules = new Jimw_Site_Module();
        $module = $this->_request->module_path;
        $modules = new Jimw_Site_Module();
        $modules->install($module);
        $this->_forward('list');
    }
}

