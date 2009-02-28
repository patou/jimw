<?php
/**
 * ManageController.php
 *
 * @author	    Patou
 * @category   Links_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: ManageController.php 92 2007-09-19 21:41:39Z patou.de.saint.steban $
 */
class Form_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
		/*$id = $this->_request->id;
		$treeFactory = new Jimw_Site_Tree();
		$result = $treeFactory->find($id);
		if (!count($result)) {
			throw new Jimw_Admin_Exception();
		}
		$tree = $result->current();
		/* @var $tree Jimw_Site_Tree_Row */
        /*$this->view->redirect_url =  $tree->getParam('url');
        $this->view->visited =  $tree->getParam('count', 0);
        $this->view->id = $id;* /
	    $config = new Zend_Config_Xml(JIMW_REP_MODULE . '/form/jimw.xml');
	    Jimw_Debug::dump($config->toArray());
	    $form = new Jimw_Form($config->module->params);
	    $submit = $form->createElement('submit', 'submit');
	    $submit->setLabel('Save');
	    $form->addElement($submit);
	    $form->populate($tree->getParams());
	    $form->setAction($this->view->url(array('controller' => 'manage', 'action' => 'save', 'module' => 'form')));
	*/
	    $this->view->form = '';
	}

	public function saveAction () {
		/*
		/* @var $tree Jimw_Site_Tree_Row * /
		$tree->setParam ('url', trim($this->_request->redirect_url, '/'));
		$tree->save();
		*/
	    /*$id = $this->_request->id;
		$treeFactory = new Jimw_Site_Tree();
		$result = $treeFactory->find($id);
		if (!count($result)) {
			throw new Jimw_Admin_Exception();
		}
		$tree = $result->current();
	    $config = new Zend_Config_Xml(JIMW_REP_MODULE . '/form/jimw.xml');
	    Jimw_Debug::dump($config->toArray());
	    $form = new Jimw_Form($config->module->params);
	    $form->isValid($this->getRequest()->getPost());
	    //if ($form->isValid($this->getRequest()->getPost())) {
	        $tree->setParams($form->getValues());
	        $tree->save();
	        Jimw_Debug::dump($tree);
	    //}
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
		*/
		$this->_forward('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>