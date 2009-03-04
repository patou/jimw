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
class FileController extends Jimw_Admin_Action
{
    public function preDispatch ()
    {
        /*$viewRenderer = $this->getHelper('ViewRenderer');
        if ($viewRenderer->getViewSuffix() == 'ajax') {
            $viewRenderer->noRenderLayout();
            $viewRenderer->setAppendBody();
        }*/
        if ($this->getHelper('Layout')->getViewSuffix() == 'ajax') {
            $this->getHelper('Layout')->disableLayout();
        }
    }
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $this->render('index');
    }
    /**
     * The default action - show the home page
     */
    public function chooserAction ()
    {
        if (isset($this->getRequest()->javascript_fct))
            $this->view->javascript_fct = $this->getRequest()->javascript_fct;
        $this->render('chooser');
    }
    public function dirchooserAction ()
    {
        if (isset($this->getRequest()->javascript_fct))
            $this->view->javascript_fct = $this->getRequest()->javascript_fct;
        $this->render('dirchooser');
    }
    public function listAction ()
    {
        $dir = $this->getRequest()->path;
        if (empty($dir))
            $dir = '/';
        $path = $this->get_dir($dir);
        date_default_timezone_set(JIMW_TIMEZONE);
        $d = new DirectoryIterator($path);
        $files['Files'] = array();
        foreach ($d as $file) {
            $filename = $file->getFilename();
            if (! $d->isDot() && $filename[0] != '.') {
                $files['Files'][] = array('name' => $filename , 'size' => $file->getSize() , 'edit' => $this->get_edit($filename) , 'lastChange' => date('D M  j h:i:s Y', $file->getATime()) , 'thumb' => ($file->isDir() ? '' : $this->get_thumb($filename, $dir)) , 'path' => '/' . trim($dir . '/' . $filename, '/') , 'url' => $this->get_url($dir . '/' . $filename) , 'iconCls' => ($file->isDir() ? 'folder' : $this->get_ext($filename)));
            }
        }
        $files['FilesCount'] = count($files['Files']);
        $this->view->files = $files;
        $this->view->file_path = $dir;
        if (! empty($dir) && $dir != '/')
            $this->view->file_path_up = substr($dir, 0, strrpos($dir, '/'));
        $this->render('list');
    }
    public function getAction ()
    {
        $dir = trim($this->getRequest()->path, '/');
        $folder = isset($this->getRequest()->folder) ? true : false;
        $path = $this->get_dir($dir);
        date_default_timezone_set(JIMW_TIMEZONE);
        $d = new DirectoryIterator($path);
        $files = array();
        foreach ($d as $file) {
            $filename = $file->getFilename();
            if (! $d->isDot() && $filename[0] != '.' && (! $folder || $file->isDir())) {
                $files[] = array('text' => $filename , 'path' => $filename , 'leaf' => ! $file->isDir() , 'edit' => $this->get_edit($filename) , 'disabled' => false , 'id' => '/' . trim($dir . '/' . $filename, '/') , 'iconCls' => ($file->isDir() ? 'folder' : $this->get_ext($filename)));
            }
        }
        $this->view->files = $files;
        $this->render('list');
    }
    public function deleteAction ()
    {
        $file = $this->get_dir($this->getRequest()->file);
        if (file_exists($file)) {
            if (is_dir($file) && @rmdir($file) || @unlink($file)) {
                $this->view->files = array('success' => true);
            } else {
                $this->view->files = array('success' => false , 'error' => "The file $file can't be deleted");
            }
        } else {
            $this->view->files = array('success' => false , 'error' => "The file $file doesn't exist");
        }
        $this->view->file_path = substr($this->getRequest()->file, 0, strrpos($this->getRequest()->file, '/'));
        $this->render('list');
    }
    public function renameAction ()
    {
        $oldname = $this->get_dir($this->getRequest()->oldname);
        $newname = $this->get_dir($this->getRequest()->newname);
        if (file_exists($oldname) && @rename($oldname, $newname)) {
            $this->view->files = array('success' => true);
        } else {
            $this->view->files = array('success' => false , 'error' => "The file $oldname can't be renamed to $newname");
        }
        $this->view->file_path = (! is_dir($oldname)) ? substr($this->getRequest()->oldname, 0, strrpos($this->getRequest()->oldname, '/')) : $this->getRequest()->oldname;
        $this->render('list');
    }
    public function newdirAction ()
    {
        $dir = $this->get_dir($this->getRequest()->dir);
        if (! @mkdir($dir, 0755, true))
            $this->view->files = array('success' => false , 'error' => "The directory $dir can't be created");
        else
            $this->view->files = array('success' => true);
        $this->view->file_path = (! is_dir($dir)) ? substr($this->getRequest()->dir, 0, strrpos($this->getRequest()->dir, '/')) : $this->getRequest()->dir;
        $this->render('list');
    }
    public function downloadAction ()
    {
        $dir = $this->_request->getBaseUrl();
        $dir = '/' . trim(substr($dir, 0, strpos($dir, 'admin')), '/');
        //$file = str_replace('../', $dir, $this->get_dir($this->_request->file));
        $file = $this->get_dir($this->_request->file);
        //$this->_redirect($file, array('exit' => true, 'prependBase' => false));
        //echo $file;
        //$this->render('list');
        //header("location: $file");
            //First, see if the file exists
    if (!is_file($file)) { die("<b>404 File $file not found!</b>"); }

    //Gather relevent info about file
    $len = filesize($file);
    $filename = basename($file);
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    //This will set the Content-Type to the appropriate setting for the file
    switch( $file_extension ) {
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      case "mp3": $ctype="audio/mpeg"; break;
      case "wav": $ctype="audio/x-wav"; break;
      case "mpeg":
      case "mpg":
      case "mpe": $ctype="video/mpeg"; break;
      case "mov": $ctype="video/quicktime"; break;
      case "avi": $ctype="video/x-msvideo"; break;

      //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
      case "php":
      case "htm":
      case "html":
      case "txt": die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;

      default: $ctype="application/force-download";
    }

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");

    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");

    //Force the download
    $header="Content-Disposition: attachment; filename=".$filename.";";
    header($header);
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    @readfile($file);
    exit;

    }
    public function uploadAction ()
    {
        $path = $this->get_dir($this->getRequest()->path);
        $return = array('success' => true);
        foreach ($_FILES as $id => $file) {
            if (! isset($file['name']) || $file['error'] != UPLOAD_ERR_OK) {
                $return['success'] = false;
                $return['errors'][$id] = 'Error to upload file ' . $file['name'] . ' ';
            } else {
                @move_uploaded_file($file['tmp_name'], $path . '/' . $file['name']);
            }
        }
        $this->view->files = $return;
        $this->view->file_path = (! is_dir($path)) ? substr($this->getRequest()->path, 0, strrpos($this->getRequest()->path, '/')) : $this->getRequest()->path;
        $this->render('list');
    }
    public function editAction ()
    {
        $filename = $this->get_dir($this->getRequest()->file);
        $ext = substr($this->get_ext($filename), 5);
        $editmode = $this->get_edit($filename);
        if ($editmode == 'edit_area') {
            $this->view->filename = $this->getRequest()->file;
            $this->view->file = file_get_contents($filename);
            if (in_array($ext, array('html' , 'htm' , 'phtml' , 'rhtml')))
                $filetype = 'html';
            elseif (in_array($ext, array('php' , 'php3' , 'php4' , 'php5' , 'php6' , 'inc')))
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
            elseif ($ext == 'pl')
                $filetype = 'perl';
            elseif ($ext == 'pas')
                $filetype = 'pas';
            else
                $filetype = 'txt';
            $this->view->filetype = $filetype;
            $this->getHelper('Layout')->disableLayout();
            $this->render('edit_area');
        } elseif ($editmode == 'jpie') {
            $file = md5($filename) . '.jpg';
            @copy($filename, JIMW_REP_JPIE_TMP . $file);
            $this->view->filename = $filename;
            $this->view->file = $file;
            $this->view->jpie_path = $this->view->path . JIMW_URL_JPIE;
            $this->render('jpie');
        }
    }
    public function saveAction ()
    {
        $filename = $this->get_dir($this->getRequest()->filename);
        $ext = substr($this->get_ext($filename), 5);
        $editmode = $this->get_edit($filename);
        if ($editmode == 'edit_area') {
            $this->getHelper('Layout')->disableLayout();
            $this->view->filename = $this->getRequest()->filename;
            $filename = $this->get_dir($this->view->filename);
            file_put_contents($filename, $this->getRequest()->file);
        } elseif ($editmode == 'jpie') {
            $file = $this->getRequest()->file;
            @copy(JIMW_REP_JPIE_TMP . $file, $filename);
        }
    }
    private function get_dir ($dir)
    {
        $site = Zend_Registry::get('site');
        $path = (isset($site->path) ? trim($site->path, '/') : 'public');
        return rtrim('../' . $path . '/' . trim($dir, '/'), '/');
    }
    private function get_ext ($file)
    {
        return preg_replace('/^.*\.([^\.]*)$/', 'file-$1', strtolower($file));
    }
    private function get_url ($path)
    {
        $dir = $this->_request->getBaseUrl();
        $dir = '/' . trim(substr($dir, 0, strpos($dir, 'admin')), '/');
        $thumb_path = $this->get_dir($path);
        $file = str_replace('../', $dir, $thumb_path);
        return $file;
    }
    private function get_thumb ($file, $path)
    {
        $dir = $this->_request->getBaseUrl();
        $dir = '/' . trim(substr($dir, 0, strpos($dir, 'admin')), '/');
        $thumb = preg_replace('/^(.*)\.([^\.]*)$/', $path . '/thumbnails/$1.jpg', $file);
        $thumb_path = $this->get_dir($thumb);
        $file_thumb = str_replace('../', $dir, $thumb_path);
        if (!file_exists($thumb_path)) {
            $file_thumb = '';
            //if (in_array($this->get_ext($file), array('file-jpg' , 'file-jpeg', 'file-png', 'file-gif')))
              //  $file_thumb = str_replace('../', $dir, $this->get_dir($path . '/' . $file));
        }

        return $file_thumb;
    }
    private function get_edit ($file)
    {
        $ext = strtolower(preg_replace('/^.*\.([^\.]*)$/', '$1', $file));
        if (in_array($ext, array('html' , 'htm' , 'phtml' , 'xml' , 'rhtml' , 'rxml' , 'rjs' , 'rb' , 'js' , 'css' , 'php' , 'py' , 'c' , 'java' , 'h' , 'txt' , 'sh' , 'sql')))
            return 'edit_area';
        if (in_array($ext, array('jpg' , 'jpeg')))
            return 'jpie';
        return 'none';
    }
}