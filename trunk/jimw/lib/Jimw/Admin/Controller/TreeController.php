<?php
/**
 * TreeController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class TreeController extends Jimw_Admin_Action
{
    public function indexAction ()
    {
        //if (!$this->_helper->getHelper('FlashMessenger')->hasMessages())
            $this->_forward('list');
        /*else
            $this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages();*/
    }
    
    public function listAction ()
    {
        $tree = new Jimw_Site_Tree();
        $site = Zend_Registry::get('site');
        $parentid = $tree->find($site->tree_id);
        if (!count($parentid))
            $parentid = $tree->getChildren(0);
        $this->view->list_tree = $parentid;
        $this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages();
    }
    
    public function trashAction ()
    {
        $tree = new Jimw_Site_Tree();
        $this->view->list_tree = $tree->fetchAll($tree->select()->where('tree_status < 0'));
        $this->view->flashmessenger = $this->_helper->getHelper('FlashMessenger')->getCurrentMessages();
        $this->render('list');
    }
    
    public function editAction ()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id');
        if (empty($id))
            $id = $req->getParam('tree_id');
        $tree = new Jimw_Site_Tree();
        $form = new Jimw_Admin_Form_TreeForm();
        $form->getElement('module_path')->setAttrib('disable', true)->setRequired(false);
        $result = $tree->fetchAll($tree->select()->where('tree_id = ?', $id));
        if (! count($result)) {
            throw new Jimw_Admin_Exception("The tree $id doesn't exist");
        }
        $result = $result->current();
        $this->_addModuleConfig($result, $form);
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $values = $form->getValues();
            $result->pagetitle = $values['tree_pagetitle'];
            $result->menutitle = $values['tree_menutitle'];
            $result->parentid = $values['tree_parentid'];
            $result->version++;
            $result->status = $values['tree_status'];
            $result->alias = $values['tree_alias'];
            $result->image = $values['tree_image'];
            //$result->icon = $values['tree_icon'];
            $result->description = $values['tree_description'];
            if (empty($values['tree_parentid'])) { // If the parent tree is the root
                $site = Zend_Registry::get('site');
                if ($result->id != $site->tree_id) { //If the node isn't the site tree root node
                    $result->parentid = $site->tree_id;
                    $result->site_id = $site->id;
                }
            }
            $parent = $tree->find($values['tree_parentid']);
            if (isset($parent) && count($parent)) {
                $result->site_id = $parent->current()->site_id;
            }
            if (JIMW_DEBUG_MODE) {
                $result->creationdate = $values['tree_creationdate'];
                $result->editiondate = $values['tree_editiondate'];
                //$result->param = $values['tree_param'];
                //$result->site_id = $values['site_id'];
            }
            //Zend_Debug::dump($save);
            if (!empty($values['tree_param'])) {
                $result->param = $values['tree_param'];
            }
            $result->save();
            $id = $result->id;
            $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $req->tree_pagetitle);
        } else {
            $form->populate($result->toArray());
        }
        $form->addSubmit('Save');
        $this->view->tree = $result;
        $this->view->form = $form;
        $this->view->id = $id;
        $this->render('form');
        if ($result->status >= 0) {
            $this->_helper->actionStack('edit', 'manage', $result->module_path);
            //$this->_forward('edit', 'manage', $result->module_path);
        }
    }
    public function addAction ()
    {
        $req = $this->getRequest();
        $tree = new Jimw_Site_Tree();
        $site = Zend_Registry::get('site');
        $form = new Jimw_Admin_Form_TreeForm();
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $save = $tree->fetchNew();
            $values = $form->getValues();
            $save->pagetitle = $values['tree_pagetitle'];
            $save->menutitle = $values['tree_menutitle'];
            $save->status = $values['tree_status'];
            $save->alias = $values['tree_alias'];
            $save->image = $values['tree_image'];
            $save->icon = $values['tree_icon'];
            $save->description = $values['tree_description'];
            $save->module_path = $req->module_path;
            $save->version = 0;
            $save->order = 1;
            $save->param = '';
            $save->icon = '';
            $save->image = '';
            $save->param = array();
            if (JIMW_DEBUG_MODE) {
                $save->creationdate = $values['tree_creationdate'];
                $save->editiondate = $values['tree_editiondate'];
                //$save->param = $values['tree_param'];
                //$result->site_id = $values['site_id'];
            }
            //Zend_Debug::dump($save);
            $save->save();
            Jimw_Debug::dump($save);
            $id = $save->id;
            //$save = $tree->find($id)->current();
            $save->parentid = $values['tree_parentid'];
            if (empty($save->parentid)) { // If the parent tree is the site root
                $save->parentid = $site->tree_id;
                $save->site_id = $site->id;
            }
            $parent = $tree->find($values['tree_parentid']);
            if (isset($parent) && count($parent)) {
                $save->site_id = $parent->current()->site_id;
            }
            else {
                $save->site_id = $site->id;
            }
            $save->save();
            $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('Insert successful') . $req->tree_pagetitle);
            $this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $id), 'format', true), array('prependBase' => false));
        } else {
            $this->view->tree = $tree->fetchNew();
            /*$module = new Jimw_Site_Module();
			$this->view->modules = $module->fetchAll();
			$this->view->form_type = 'insert';*/
            $form->addSubmit('Insert');
            $form->populate(array('tree_status' => Jimw_Site_Tree::PUBLISHED, 'site_id' => $site->id));
            $this->view->form = $form;
            $this->view->id = '';
            $this->render('form');
        }
    }
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $tree = new Jimw_Site_Tree();
        $result = $tree->fetchAll($tree->select()->where('tree_id = ?', $id));
        if (! count($result)) {
            throw new Jimw_Admin_Exception("This tree doesn't exist");
        }
        $result = $result->current();
        $module_path = $result->module_path;
        if ($result->status == Jimw_Site_Tree::DELETED) { //Delete from the database
            $result->delete();
            //TODO Delete the arborescence !
            $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful');
            $this->_forward('delete', 'manage', $module_path);
        }
        else {
            $result->status = Jimw_Site_Tree::DELETED;
            $result->save();
            $this->_helper->getHelper('FlashMessenger')->addMessage('Move to the trash');
            $this->_forward('list');
        }
    }
    
	public function restoreAction ()
    {
        $id = $this->_request->id;
        $tree = new Jimw_Site_Tree();
        $result = $tree->fetchAll($tree->select()->where('tree_id = ?', $id));
        if (! count($result)) {
            throw new Jimw_Admin_Exception("This tree doesn't exist");
        }
        $result = $result->current();
        $module_path = $result->module_path;
        $result->status = Jimw_Site_Tree::UNPUBLISHED;
        $result->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Restore successful');
        $this->_forward('list');
    }
    
	public function publishAction ()
    {
        $id = $this->_request->id;
        $tree = new Jimw_Site_Tree();
        $result = $tree->fetchAll($tree->select()->where('tree_id = ?', $id));
        if (! count($result)) {
            throw new Jimw_Admin_Exception("This tree doesn't exist");
        }
        $result = $result->current();
        $module_path = $result->module_path;
        $result->status = Jimw_Site_Tree::PUBLISHED;
        $result->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Publish successful');
        $this->_forward('list');
    }
    
	public function archiveAction ()
    {
        $id = $this->_request->id;
        $tree = new Jimw_Site_Tree();
        $result = $tree->fetchAll($tree->select()->where('tree_id = ?', $id));
        if (! count($result)) {
            throw new Jimw_Admin_Exception("This tree doesn't exist");
        }
        $result = $result->current();
        $module_path = $result->module_path;
        $result->status = Jimw_Site_Tree::ARCHIVED;
        $result->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Move to the trash');
        $this->_forward('list');
    }
   /* public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $tree = new Jimw_Site_Tree();
        $save = $tree->find($id)->current();
        $save->id = $id;
        $save->pagetitle = $req->pagetitle;
        $save->menutitle = $req->menutitle;
        $save->order = 0;
        $save->parentid = $req->parentid;
        $save->alias = $req->alias;
        $save->description = $req->description;
        $save->status = $req->status;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $req->pagetitle);
        $this->_forward('index');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        //Zend_Debug::dump($req);
        $tree = new Jimw_Site_Tree();
        $save = $tree->fetchNew();
        $save->pagetitle = $req->pagetitle;
        $save->menutitle = $req->menutitle;
        $save->order = 0;
        $save->parentid = $req->parentid;
        $save->module_id = $req->getPost('module');
        $site = Zend_Registry::get('site');
        $save->site_id = $site->id;
        $save->version = 0;
        $save->status = 4;
        $save->param = '';
        $save->icon = '';
        $save->image = '';
        //$save->creationdate = '1900-01-01 00:00:00';
        //$save->editiondate = '1900-01-01 00:00:00';
        $save->alias = $req->alias;
        $save->description = $req->description;
        //Zend_Debug::dump($save);
        $save->save();
        $id = $save->id;
        $this->_helper->getHelper('FlashMessenger')->addMessage('Insert successful ' . $req->pagetitle);
        $this->_forward('edit', 'tree', 'default', array('id' => $id));
        //$this->_forward('index');
    }*/
    public function moveAction ()
    {
        $tree = new Jimw_Site_Tree();
        $save = $tree->find($this->_request->id)->current();
        if ($this->_request->dir == 'down') {
            $where = array('tree_lft = ?' => $save->rgt + 1);
            $direction = Jimw_Site_Tree::RIGHT;
        } else {
            $where = array('tree_rgt = ?' => $save->lft - 1);
            $direction = Jimw_Site_Tree::LEFT;
        }
        $sibling = $tree->fetchAll($where);
        if (count($sibling))
            $tree->move_to($save, $sibling->current(), $direction);
        else
            throw new Jimw_Admin_Exception('Illegal move');
        $this->_helper->getHelper('FlashMessenger')->addMessage('Move successful ' . $save->pagetitle);
        $this->_forward('index');
    }

    public function chooserAction()
    {
        if (isset($this->getRequest()->javascript_fct))
            $this->view->javascript_fct = $this->getRequest()->javascript_fct;
        if ($this->getHelper('Layout')->getViewSuffix() == 'ajax') {
            $this->getHelper('Layout')->disableLayout();
        }
        $this->render('chooser');
    }

    public function getAction()
    {
        $id = $this->getRequest()->path;
        $tree = new Jimw_Site_Tree();
        $parentid = $tree->fetchAll($tree->select()->where('tree_parentid = ?', $id)->order(array('tree_lft' , 'tree_alias'))); //$tree->select()->where('tree_parentid = ?', $id)->where('tree_status = ?', Jimw_Site_Tree::PUBLISHED)
        $this->view->list_tree = $parentid;
    }

    private function _addModuleConfig(Jimw_Site_Tree_Row $tree, Jimw_Form $form)
    {
        $module = $tree->findParentJimw_Site_Module();
        if ($module && isset($module->xml->controllers->{$module->path}->params)) {
            $params = $module->xml->controllers->{$module->path};
            $formModule = new Jimw_Form_SubForm($params->params);
            $formModule->setLegend($module->name);
            $form->addSubForm($formModule, 'tree_param');
        }
    }
}
?>