<?php
/**
 * SiteController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

/**
 * This controler allow to administrate the list of site in the database of the current website.
 *
 */
class SiteController extends Jimw_Admin_Action
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

    /**
     * List all site
     *
     */
    public function listAction ()
    {
        $sites = new Jimw_Site_Site();
        $this->view->sites_list = $sites->fetchAll();
    }

    /**
     * Edit site
     *
     */
    public function editAction ()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id');
        if (! $id)
            $id = $req->getParam('site_id', 1);
        $sites = new Jimw_Site_Site();
        $result = $sites->find($id);
        if (! count($result)) {
            Jimw_Debug::dump($result);
            throw new Jimw_Admin_Exception($this->_("This site doesn't exist"));
        }
        $save = $result->current();
        $domainTable = new Jimw_Site_Domain();
        $current_domain = $domainTable->find($save->domain_id)->current();
        $form = new Jimw_Admin_Form_SiteForm();
        $form->getElement('default_tree_id')->setSiteId($id);
        $form->getElement('tree_id')->setAttrib('disable', true)->setRequired(false);
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $values = $form->getValues();
            $save->default_tree_id = $values['default_tree_id'];
            $save->name = $values['site_name'];
            if (!empty($values['domain']) && ($current_domain->__toString() != $values['domain'])) {
	            $domain = $domainTable->createFromUrl($values['domain']);
	            $database = Zend_Registry::get('database');
		        $domain->site_id = $id;
		        $domain->database_id = $database->id;
		        $domain->status = 1;
	            $domain->save();
	            $domainTable->clearCache();
	            $save->domain_id = $domain->id;
	            $current_domain->delete();
            }
            $save->path = trim($values['site_path'], '/');
            $save->template = trim($values['site_template'], '/');
            $save->save();
            $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
            $this->_forward('index');
        } else {
            $form->populate($save->toArray());
            $form->populate(array('domain' => $current_domain->__toString()));
            $form->addSubmit('Edit');
            $this->view->form = $form;
            $this->render('form');
        }
    }

    public function addAction ()
    {
        $req = $this->getRequest();
        $sites = new Jimw_Site_Site();
        $form = new Jimw_Admin_Form_SiteForm();
        $form->getElement('default_tree_id')->setAttrib('disable', true)->setRequired(false);
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $values = $form->getValues();
            $save = $sites->fetchNew();
            $save->tree_id = $values['tree_id'];
            $save->default_tree_id = $values['tree_id'];
            //$save->domain_id = $values['domain_id'];
            $save->access = 1;
            $save->name = $values['site_name'];
            $save->path = trim($values['site_path'], '/');
            $save->template = trim($values['site_template'], '/');
            $save->save();
            $id = $save->id;
            $trees = new Jimw_Site_Tree();
            $domainTable = new Jimw_Site_Domain();
            $domain = $domainTable->createFromUrl($values['domain']);
            $database = Zend_Registry::get('database');
	        $domain->site_id = $id;
	        $domain->database_id = $database->id;
	        $domain->status = 1;
            $domain->save();
            $save->domain_id = $domain->id;
            $save->save();
            $tree = $trees->find($save->tree_id)->current();
            $where = null;
            if ($tree) {
                $where = array('site_id = ?' => $tree->site_id);
            }
            else {
                $tree = 0;
            }
            $trees->updateAllChildren($tree, array('site_id' => $id), $where);
            $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
            $this->_forward('index');
        } else {
            $form->addSubmit('Insert');
            $this->view->form = $form;
            $this->render('form');
        }
    }

    public function deleteAction ()
    {
        $id = $this->_request->id;
        $sites = new Jimw_Site_Site();
        $site = $sites->find($id);
        if (! count($site)) {
            throw new Jimw_Admin_Exception("The site $id doesn't exist");
        }
        if ($site->access == 1) {
            throw new Jimw_Admin_Exception("You can't delete the principal site (use the install administration instead)");
        }
        $current_site = Zend_Registry::get('site');
        if ($site->id == $current_site->id) {
            throw new Jimw_Admin_Exception("You can't delete the current site");
        }
        $site = $site->current();
        $id = $site->id;
        $trees = new Jimw_Site_Tree();
        $tree = $trees->find($site->tree_id);
        if ($tree != null && count($tree)) {
            $tree = $tree->current();
            $parent = $tree->getParent();
            $parent_id = 0;
            if ($parent != null && count($parent)) {
                $parent_id = $parent->current()->site_id;
            }
            $trees->updateAllChildren($tree, array('site_id' => $parent_id), array('site_id = '.$tree->site_id));
        }
        $domainTable = new Jimw_Site_Domain();
        $database = Zend_Registry::get('database');
        $domainTable->delete(array('site_id = ?' => $id, 'database_id = ?' => $database->id));
        $site->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful');
        $this->_forward('list');
    }
}

