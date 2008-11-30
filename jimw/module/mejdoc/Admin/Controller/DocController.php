<?php
/**
 * DocController.php
 *
 * @author	   Logisim
 * @category   Mejdoc_Admin
 * @package    Mej_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
include_once (dirname(__FILE__) . '/../../Controller/Model/Mejdoc.php');
class Mejdoc_DocController extends Jimw_Admin_Action
{
	public function listAction () {
		$documents = new Mejdoc();
		$this->view->documents_list = $documents->fetchAll(array('tree_id = ?' => $this->view->id), 'mejdoc_order');
	}

	public function editAction () {
		$id = $this->_request->id;
		$this->view->request = $this->_request;
		$this->view->id = $id;
		$documents = new Mejdoc();
		$document = $documents->fetchRow(array('mejdoc_id = ?' => $id));
		if (!$document)
			throw new Jimw_Admin_Exception('This document doesn\'t exist');
		$document->publishdate = @date('Y-m-d H:i:s', time());
		$this->view->document = $document;
		$this->view->form_type = 'save';
		$this->render('form');
	}

	public function saveAction () {
		$id = $this->_request->id;
		$documents = new Mejdoc();
		$document = $documents->fetchRow(array('mejdoc_id = ?' => $id));
		$document->title = $this->_request->title;
		$document->comment = $this->_request->comment;
		$document->editiondate = $this->_request->editiondate;
		$document->publishdate = $this->_request->publishdate;

		if (isset($_FILES['file'])) {
			// Sending file
			$path = $this->get_dir('doc');
			$file = $_FILES['file'];
			$filename = $this->cleanFileName($file['name']);
			if (isset($file['name']) && $file['error'] == UPLOAD_ERR_OK) {
				move_uploaded_file($file['tmp_name'], $path . '/' . $filename);
				$document->file = $filename;
			} else {
				$document->file = '';
			}
		}

		$document->save ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Save successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $document->tree_id));
	}

	public function insertAction () {
		$id = $this->_request->id;
		$documents = new Mejdoc();
		$document = $documents->fetchNew();
		$document->tree_id = $this->_request->treeid;
		$document->title = $this->_request->title;
		$document->comment = $this->_request->comment;
		$document->editiondate = $this->_request->editiondate;
		$document->publishdate = $this->_request->publishdate;

		// Sending file
		$path = $this->get_dir('doc');
		$file = $_FILES['file'];
		$filename = $this->cleanFileName($file['name']);
		if (isset($file['name']) && $file['error'] == UPLOAD_ERR_OK) {
			move_uploaded_file($file['tmp_name'], $path . '/' . $filename);
			$document->file = $filename;
		} else {
			$document->file = '';
		}

		// Order
		$document->order = $this->get_order($this->_request->treeid);

		$document->save();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Insert successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $document->tree_id));
	}

	public function addAction () {
		$documents = new Mejdoc();
		$document = $documents->fetchNew();
		$document->tree_id = $this->_request->getParam('id');
		$document->editiondate = @date('Y-m-d H:i:s', time());
		$document->publishdate = @date('Y-m-d H:i:s', time());
		$this->view->document = $document;
		$this->view->form_type = 'insert';
		$this->view->id = '';
		$this->render('form');
	}

	public function deletefileAction () {
		$id = $this->_request->id;
		$documents = new Mejdoc();
		$document = $documents->find($id);
		if (!count($document)) {
			throw new Jimw_Admin_Exception('This document doesn\'t exist');
		}
		$document = $document->current();
		// Deleting file
		$path = $this->get_dir('doc');
		unlink($path.'/'.$document->file);
		$document->file = '';
		$document->save ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful');
		$this->_forward('edit', 'doc', 'mejdoc', array('id' => $id));
	}

	public function deleteAction () {
		$id = $this->_request->id;
		$documents = new Mejdoc();
		$document = $documents->find($id);
		if (!count($document)) {
			throw new Jimw_Admin_Exception('This document didn\'t exist');
		}
		$document = $document->current();
		$documentid = $document->tree_id;
		// Deleting file
		$path = $this->get_dir('doc');
		unlink($path.'/'.$document->file);
		$document->delete ();
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Delete successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $documentid));
	}

	public function moveAction () {

		$tree = new Jimw_Site_Tree();
		$documents = new Mejdoc();
		$doc1 = $documents->fetchRow(array('mejdoc_id = ?' => $this->_request->id));
		$treeid = $doc1->tree_id;
		if ($this->_request->dir == 'down') {
			$doc2 = $documents->fetchRow(array('mejdoc_order = ?' => $doc1->order+1));
		} else {
			$doc2 = $documents->fetchRow(array('mejdoc_order = ?' => $doc1->order-1));
		}
		if ($doc2) {
			$temp = $doc2->order;
			$doc2->order = $doc1->order;
			$doc1->order = $temp;
			$doc1->save();
			$doc2->save();
		} else
			throw new Jimw_Admin_Exception('Illegal move');
		$this->_helper->getHelper('FlashMessenger')->addMessage ('Move successful');
		$this->_forward('edit', 'tree', 'default', array('id' => $treeid));
	}

	private function get_dir ( $dir ) {
		//$session = new Zend_Session_Namespace('Admin');
		$site = Zend_Registry::get('site');
		$path = (isset($site->path) ? trim($site->path, '/') : 'public');
		return rtrim('../' . $path . '/' . trim($dir, '/'), '/');
	}

	private function cleanFileName($str) {
		return strtr(strtolower($str), 'àâäãçéèêëîïòôöõùûüñÀÂÄÃÇÉÈÊËÎÏÒÔÖÕÙÛÜÑ \'', 'aaaaceeeeiioooouuunAAAACEEEEIIOOOOUUUN__');
	}

	private function get_order($id) {
		$max = 0;
		$documents = new Mejdoc();
		$result = $documents->fetchAll(array('tree_id = ?' => $id), 'mejdoc_order DESC');
		foreach ($result as $document) {
		   if ($max < $document->order) $max = $document->order;
		}
		return $max+1;
	}

}

?>
