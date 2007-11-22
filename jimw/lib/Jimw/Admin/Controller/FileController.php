<?php

/**
 * FileController
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class FileController extends Jimw_Admin_Action {

	public function preDispatch () {
		$viewRenderer = $this->getHelper ( 'ViewRenderer' );
		if ($viewRenderer->getViewSuffix()== 'ajax') {
			$viewRenderer->noRenderLayout ();
			$viewRenderer->setAppendBody ();
		}
	}

	/**
	 * The default action - show the home page
	 */
	public function indexAction () {
		$this->getHelper ( 'ViewRenderer' )->noRenderLayout ();
		$this->render ( 'index' ) ;
	}
	
	public function listAction () {
		$dir = $this->getRequest()->path;
		if (empty($dir))
			$dir = '/'; 
		$path = $this->get_dir($dir);
		date_default_timezone_set('Europe/Paris');
		$d = new DirectoryIterator($path);
		$files['Files'] = array ();
		foreach ($d as $file) {
			$filename = $file->getFilename();
			if (!$d->isDot () && $filename[0] != '.') {
				$files['Files'][] = array('name' => $filename,
										'size' => $file->getSize (),
										'lastChange' => date('D M  j h:i:s Y', $file->getATime ()),
										'path' => '/' . trim($dir . '/' . $filename, '/'),
										'cls' => ($file->isDir () ? 'folder' : $this->get_ext($filename)));
			}
		}
		$files['FilesCount'] = count($files['Files']);
		$this->view->files = $files;
		$this->view->file_path = $dir;
		if (!empty($dir) && $dir != '/')
			$this->view->file_path_up = substr($dir, 0, strrpos($dir, '/'));
		$this->render('list');
	}
	
	public function getAction () {
		$dir = $this->getRequest()->path;
		$path = $this->get_dir($dir);
		date_default_timezone_set('Europe/Paris');
		$d = new DirectoryIterator($path);
		$files = array ();
		foreach ($d as $file) {
			$filename = $file->getFilename();
			if (!$d->isDot () && $filename[0] != '.') {
				$files[] = array('text' => $filename,
								'path' => $filename,
								'leaf' => !$file->isDir (),
								'disabled' => false,
								'id' => '/' . trim($dir . '/' . $filename, '/'),
								'cls' => ($file->isDir () ? 'folder' : $this->get_ext($filename)));
			}
		}
		$this->view->files = $files;
		$this->render('list');
	}
	
	public function deleteAction () {
		$file = $this->get_dir($this->getRequest()->file);
		if (file_exists($file)) {
			if (is_dir($file) && @rmdir($file) || @unlink($file)) {
				$this->view->files = array('success' => true);	
			}		
			else {
				$this->view->files = array('success' => false, 'error' => "the file $file can't be deleted");
			}			
		}
		else {
			$this->view->files = array('success' => false, 'error' => "the file $file didn't exist");
		}
		$this->view->file_path = substr($this->getRequest()->file, 0, strrpos($this->getRequest()->file, '/'));
		$this->render('list');
	}
	
	public function renameAction () {
		$oldname = $this->get_dir($this->getRequest()->oldname);
		$newname = $this->get_dir($this->getRequest()->newname);
		if (file_exists($oldname) && @rename($oldname, $newname)) {
			$this->view->files = array('success' => true);		
		}
		else {
			$this->view->files = array('success' => false, 'error' => "the file $oldname can't be renamed to $newname");
		}
		$this->view->file_path = (!is_dir($oldname)) ? substr($this->getRequest()->oldname, 0, strrpos($this->getRequest()->oldname, '/')) : $this->getRequest()->oldname;
		$this->render('list');
	}
	
	public function newdirAction () {
		$dir = $this->get_dir($this->getRequest()->dir);
		if (!@mkdir($dir, 0755, true))
			$this->view->files = array('success' => false, 'error' => "the directory $dir can't be create");
		else
			$this->view->files = array('success' => true);
		$this->view->file_path = (!is_dir($dir)) ? substr($this->getRequest()->dir, 0, strrpos($this->getRequest()->dir, '/')) : $this->getRequest()->dir;
		$this->render('list');
	}
	
	public function downloadAction () {
		$dir = $this->_request->getBaseUrl ();
		$dir = '/' . trim(substr($dir, 0, strpos($dir, 'admin')), '/');
		$file = str_replace('../', $dir, $this->get_dir($this->_request->file));
		//$this->_redirect($file, array('exit' => true, 'prependBase' => false));
		//echo $file;
		//$this->render('list');
		header("location: $file");
	}
	
	public function uploadAction () {
		$path = $this->get_dir($this->getRequest()->path);
		$return = array('success' => true);
		foreach ($_FILES as $id => $file) {
			if (!isset($file['name']) || $file['error'] != UPLOAD_ERR_OK) {
				$return['success'] = false;
				$return['errors'][$id] = 'Error to upload file ' . $file['name'] . ' ';
			}
			else {
				@move_uploaded_file($file['tmp_name'], $path . '/' . $file['name']);	
			}			
		}
		$this->view->files = $return;
		$this->view->file_path = (!is_dir($path)) ? substr($this->getRequest()->path, 0, strrpos($this->getRequest()->path, '/')) : $this->getRequest()->path;
		$this->render('list');
	}
	
	private function get_dir ( $dir ) {
		$session = new Zend_Session_Namespace('Admin');
		$path = (isset($session->site->path) ? trim($session->site->path, '/') : 'public');
		return rtrim('../' . $path . '/' . trim($dir, '/'), '/');
	}
	
	private function get_ext ( $file ) {
		return preg_replace('/^.*\.([^\.]*)$/', 'file-$1', $file);
	}
}