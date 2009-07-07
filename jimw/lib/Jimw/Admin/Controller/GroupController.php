<?php
/**
 * GroupController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class GroupController extends Jimw_Admin_Action
{
    public function preDispatch()
    {
        $this->checkRoleAllowed('admin_user');
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
        $groups = new Jimw_Site_Group();
        $this->view->groups_list = $groups->fetchAll();
    }
    public function editAction ()
    {
        $req = $this->_request;
        $id = $req->getParam('id');
        $groups = new Jimw_Site_Group();
        $group = $groups->find($id);
        if (! count($group)) {
            throw new Jimw_Admin_Exception($this->_("This group doesn't exist"));
        }
        $group = $group->current();
        if ($group->type == 1) {
            throw new Jimw_Admin_Exception($this->_("You can't edit a system group"));
        }
        $form = new Jimw_Admin_Form_GroupForm();
        if ($req->isPost() && $form->isValid($req->getPost()))
        {
            $values = $form->getValues();
            $group->id = $id;
	        $group->name = $values['group_name'];
            if (!empty($values['group_parents']) && is_array($values['group_parents'])) {
    	        $options = $form->getElement('group_parents')->getUsergroupList();
    	        $parents = array();
    	        foreach ($values['group_parents'] as $group_id) {
                    $parents[] = $options[$group_id];
    	        }
    	        $group->parents = implode(',', $parents);
            }
            else {
                $group->parents = '';
            }
	        $group->save();
	        $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('Save successful') . ' ' . $group->id);
	        $this->_forward('index');
        }
        else
        {
            $array = $group->toArray();
            $array['group_parents'] = array();
            $element = $form->getElement('group_parents');
            $options = $element->getUsergroupList();
            $parents = explode(',', $group->parents);
            foreach ($parents as $parent) {
                $key = array_search($parent, $options);
                if ($key)
                    $array['group_parents'][] = $key;
            }
            $element->removeMultiOption(array_search($group->name, $options));
            $form->populate($array);
            $form->addSubmit('Save');
	        $this->view->group = $group;
	        $this->view->form = $form;
	        $this->view->id = $id;
	        $this->render('form');
        }
    }
    public function addAction ()
    {
        $req = $this->_request;
        $groups = new Jimw_Site_Group();
        $group = $groups->fetchNew();
        $form = new Jimw_Admin_Form_GroupForm();
        if ($req->isPost() && $form->isValid($req->getPost()))
        {
            $values = $form->getValues();
            $group->name = $values['group_name'];
            if (!empty($values['group_parents']) && is_array($values['group_parents'])) {
    	        $options = $form->getElement('group_parents')->getUsergroupList();
    	        $parents = array();
    	        foreach ($values['group_parents'] as $group_id) {
                    $parents[] = $options[$group_id];
    	        }
    	        $group->parents = implode(',', $parents);
            }
            else {
                $group->parents = '';
            }
	        $group->save();
	        $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('Add successful') . ' ' . $group->id);
	        $this->_forward('index');
        }
        else
        {
            $form->populate($group->toArray());
            $form->addSubmit('Insert');
            $this->view->form = $form;
            $this->render('form');
        }
    }
    /*
    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $groups = new Jimw_Site_Group();
        $save = $groups->find($id)->current();
        $save->id = $id;
        $save->name = $req->name;
        $save->parents = $req->parents;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }
    public function insertAction ()
    {
        $req = $this->_request;
        $group = new Jimw_Site_Group();
        $save = $group->fetchNew();
        $save->name = $req->name;
        $save->type = 0;
        $save->parents = $req->parents;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index');
    }*/
    public function deleteAction ()
    {
        $id = $this->_request->id;
        $groups = new Jimw_Site_Group();
        $group = $groups->find($id);
        if (! count($group)) {
            throw new Jimw_Admin_Exception($this->_("This site doesn't exist"));
        }
        $group = $group->current();
        if ($group->type == 1) {
            throw new Jimw_Admin_Exception($this->_("You can't delete a system group"));
        }
        $url = $group->id;
        $group->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('Delete successful') . ' ' . $url);
        $this->_forward('list');
    }
}

