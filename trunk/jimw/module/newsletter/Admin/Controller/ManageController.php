<?php
/**
 * indexController.php
 *
 * @author	   Logisim
 * @category   Blog_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Newsletter_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
		Jimw_Debug::dump($this->getRequest()->getParams());
		$this->_forward('newsletter', 'newsletter', 'newsletter', array('alias' => $this->getRequest()->getParam('alias'), 'id' => $this->getRequest()->getParam('id')));
	}

	public function deleteAction () {
		$newsletters = new newsletter_Model_Newsletter();
		$list = $newsletters->fetchAll(array('tree_alias = ?' => $this->getRequest()->alia));
		foreach ($list as $newsletter) {
			$newsletters->delete();
		}
		$newsletterusers = new newsletter_Model_Newsletteruser();
		$list = $newsletterusers->fetchAll(array('tree_alias = ?' => $this->getRequest()->alia));
		foreach ($list as $newsletteruser) {
			$newsletteruser->delete();
		}
	  	$this->_forward('list', 'tree', 'default');
	}
}
?>