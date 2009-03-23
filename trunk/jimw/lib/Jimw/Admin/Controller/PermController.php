<?php
/**
 * PermController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class PermController extends Jimw_Admin_Action
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

    }

    public function editAction ()
    {
        $request = $this->getRequest();
        $id = $request->getParam('group_id');
        $site = 1;
        $perms = new Jimw_Site_Siteperm();
        $perm = $perms->fetchRow(array('site_id = ?' => $site , 'group_id = ?' => $id));
        if (!$perm) {
            // on la cré !
            $perm = $perms->fetchNew();
            $perm->site_id = $site;
            $perm->group_id = $id;
            $perm->perm = array();
            $perm->save();
        }
        $form = new Jimw_Admin_Form_PermForm();
        $acl = new Jimw_Acl();
        $form->addRessourceForm($acl->getRessourcelist());
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $perm->perm = $form->getValues();
            $perm->save();
        }
        else {
            $form->populate($perm->perm->toArray());
        }
        Jimw_Debug::dump($form);
        $form->addSubmit('Save');
        $this->view->perm = $perm;
        $this->view->form = $form;
        $this->view->id = $perm->id;
        $this->render('edit');
    }

    public function saveAction ()
    {
        $req = $this->_request;
        $id = $req->id;
        $perms = new Jimw_Site_SitePerm();
        $save = $perms->find($id)->current();
        $save->id = $id;
        $save->allow = $req->allow;
        $save->admin = $req->admin;
        $save->save();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Save successful ' . $save->id);
        $this->_forward('index', 'group');
    }

    public function deleteAction ()
    {
        $id = $this->_request->id;
        $perms = new Jimw_Site_Perm();
        $perm = $perms->find($id);
        if (! count($perm)) {
            throw new Jimw_Admin_Exception("The site $id doesn't exist");
        }
        $perm = $perm->current();
        if ($perm->type == 1) {
            throw new Jimw_Admin_Exception("You can't delete a system perm");
        }
        $url = $perm->id;
        $perm->delete();
        $this->_helper->getHelper('FlashMessenger')->addMessage('Delete successful ' . $url);
        $this->_forward('list');
    }
}

