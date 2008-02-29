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
	
	/**
	 * The default action - show the home page
	 */
	public function chooserAction () {
		$this->getHelper ( 'ViewRenderer' )->noRenderLayout ()->setAppendBody ();
		$this->render ( 'chooser' ) ;
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
										'edit' => $this->get_edit($filename),
										'lastChange' => date('D M  j h:i:s Y', $file->getATime ()),
										'thumb' => ($file->isDir () ? '' : $this->get_thumb($filename, $dir)),
										'path' => '/' . trim($dir . '/' . $filename, '/'),
										'url' => $this->get_url($dir . '/' . $filename),
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
		$folder = isset($this->getRequest()->folder) ? true : false;
		$path = $this->get_dir($dir);
		date_default_timezone_set('Europe/Paris');
		$d = new DirectoryIterator($path);
		$files = array ();
		foreach ($d as $file) {
			$filename = $file->getFilename();
			if (!$d->isDot () && $filename[0] != '.' && (!$folder || $file->isDir ())) {
				$files[] = array('text' => $filename,
								'path' => $filename,
								'leaf' => !$file->isDir (),
								'edit' => $this->get_edit($filename),
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

	public function editAction () {
		$filename = $this->get_dir($this->getRequest()->file);
		$ext = substr($this->get_ext($filename), 5);
		$editmode = $this->get_edit($filename);
		if ($editmode == 'edit_area') {
			$this->view->filename = $this->getRequest()->file;
			$this->view->file = file_get_contents($filename);
			if (in_array($ext, array('html', 'htm', 'phtml', 'rhtml')))
				$filetype = 'html';
			elseif (in_array($ext, array('php', 'php3', 'php4', 'php5', 'php6', 'inc')))
				$filetype = 'php';
			elseif ($ext == 'js')
				$filetype = 'js';
			elseif ($ext == 'css')
				$filetype = 'css';
			elseif ($ext == 'rb')
				$filetype = 'ruby';
			elseif ($ext == 'py')
				$filetype = 'python';
			elseif ($ext == 'c' || $ext == 'h')
				$filetype = 'c';
			elseif ($ext == 'cpp' || $ext == 'hpp' || $ext == 'cc' || $ext == 'hh')
				$filetype = 'cpp';
			elseif ($ext == 'sql')
				$filetype = 'sql';
			elseif ($ext == 'vb')
				$filetype = 'vb';
			elseif ($ext == 'xml')
				$filetype = 'xml';
			elseif ($ext == 'bas')
				$filetype = 'basic';
			else
				$filetype = 'txt';
			$this->view->filetype = $filetype;
			$this->getHelper('ViewRenderer')->noRenderLayout();
			$this->getHelper('ViewRenderer')->noRenderLayout();
			$this->render('edit_area');
		}
	}
	public function saveAction() {
		$this->view->filename = $this->getRequest()->filename;
		$filename = $this->get_dir($this->view->filename);
		file_put_contents($filename, $this->getRequest()->file);
	}
	private function get_dir ( $dir ) {
		$session = new Zend_Session_Namespace('Admin');
		$site = Zend_Registry::get('site');
		$path = (isset($site->path) ? trim($site->path, '/') : 'public');
		return rtrim('../' . $path . '/' . trim($dir, '/'), '/');
	}
	
	private function get_ext ( $file ) {
		return preg_replace('/^.*\.([^\.]*)$/', 'file-$1', strtolower($file));
	}
	
	private function get_url ($path) {
		$dir = $this->_request->getBaseUrl ();
		$dir = '/' . trim(substr($dir, 0, strpos($dir, 'admin')), '/');
		$thumb_path = $this->get_dir($path);
		$file = str_replace('../', $dir, $thumb_path);
		return $file;
	}	
	private function get_thumb ($file, $path) {
		$dir = $this->_request->getBaseUrl ();
		$dir = '/' . trim(substr($dir, 0, strpos($dir, 'admin')), '/');
		$thumb = preg_replace('/^(.*)\.([^\.]*)$/', $path.'/thumbnails/$1.jpg', $file);
		$thumb_path = $this->get_dir($thumb);
		$file = str_replace('../', $dir, $thumb_path);
		return file_exists($thumb_path) ? $file : '';
	}
	
	private function get_edit ($file) {
		$ext = strtolower(preg_replace('/^.*\.([^\.]*)$/', '$1', $file));
		if (in_array($ext, array('html', 'htm', 'phtml', 'xml', 'rhtml', 'rxml', 'rjs', 'rb', 'js', 'css', 'php', 'py', 'c', 'java', 'h', 'txt', 'sh', 'sql')))
			return 'edit_area';
		return 'none';
	}
}