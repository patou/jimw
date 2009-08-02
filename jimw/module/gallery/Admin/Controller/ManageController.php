<?php
/**
 * ManageController.php
 *
 * @author	    Patou
 * @category   Gallery_Admin
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Gallery_ManageController extends Jimw_Admin_Action
{
	public function editAction () {
	    $request = $this->getRequest();
	    /** @var $tree Jimw_Site_Tree_Row */
	    $tree = $request->tree;
	    $rep = trim($tree->getParam('dir'), '/');
	    $dir = rtrim($tree->findParentJimw_Site_Site()->path, '/') . '/' . $rep;
	    $path = JIMW_ROOT . $dir;
		$base_path = str_replace('/admin', '', $request->getBaseUrl()) . '/' . $dir . '/';
		if (is_dir($path)) {
			if ($dh = opendir($path)) {
				while (($img = readdir($dh)) !== false) {
					if ($img != '.' && $img != '..' && !is_dir($path . '/' . $img)) {
					    $image = new Jimw_Image($path . '/' . $img, $base_path);
					    if ($image->hasThumbnails(true)) {
					        $size = getimagesize($image->getThumbnailsFilename());
							$photos[] = array ('url' => $image->getUrl(), 'title' => '', 'thumbnails' => $image->getThumbnailsUrl(), 'thumbnails_width' => $size[0], 'thumbnails_height' => $size[1], 'file' => $image->getFilename());
						}
					}
				}
				closedir($dh);
			}
		}
		//else
			//throw new Jimw_Admin_Exception("$path isn't a valid directory");
		function picturesort($a, $b) {
		    return ($a['file'] < $b['file']) ? -1 : 1;
		}
		usort($photos, picturesort);
		$this->view->photos = $photos;
		$this->view->photos_path = $path;
		$this->view->dir = str_replace(JIMW_URL_PUBLIC_PATH, '', '/'. $dir);
	}
	
	public function deletefileAction() {
	    $request = $this->getRequest();
	    if (!$request->isPost() || empty($request->dir)) {
	        throw new Jimw_Admin_Exception('Action deleteFile error');
	    }
        
        $dir = $request->dir;
        $fm = $this->_helper->getHelper('FlashMessenger');
        foreach ($request->files as $file) {
            $image = new Jimw_Image($dir . '/' . $file, '');
            unlink($image->getFile());
            if ($image->hasThumbnails(false)) {
        	    unlink($image->getThumbnailsFilename());
            }
        	$fm->addMessage('Delete successful ' . $file);
        }
        //$this->_redirect($this->view->url(array('action'=>'edit', 'controller' => 'tree', 'module' => 'default', 'id' => $request->tree_id), 'format', true), array('prependBase' => false));
        $this->_forward ('index', 'tree', 'default', array('id' => $request->tree_id));
	}

    public function uploadfileAction ()
    {
        $request = $this->getRequest();
	    if (!$request->isPost() || empty($request->path)) {
	        throw new Jimw_Admin_Exception('Action deleteFile error');
	    }
        $path = $request->path;
        $fm = $this->_helper->getHelper('FlashMessenger');
        foreach ($_FILES as $id => $file) {
            if (! isset($file['name'])) {
                if ($file['error'] != UPLOAD_ERR_OK) {
                   $fm->addMessage('Error to upload file ' . $file['name'] . ' ');
                }
            } else {
                @move_uploaded_file($file['tmp_name'], $path . '/' . $file['name']);
                $fm->addMessage('Upload successful ' . $file['name']);
            }
        }
        $this->_forward ('index', 'tree', 'default', array('id' => $request->tree_id));
    }
	
	public function saveAction () {
		$this->_forward ('index', 'tree', 'default', array('id' => $this->_request->id));
	}
}
?>