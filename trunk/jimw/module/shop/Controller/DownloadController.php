<?php
/**
 * DownloadController
 *
 * @author	   Logisim
 * @category   Jimw_Shop
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: $
 */

include_once('Model/ShopAlbum.php');
include_once('Model/ShopSong.php');
include_once('Model/ShopSongOrder.php');
class Shop_DownloadController extends Jimw_Module_Action
{
	private static $tabext = array(
	  'bz2' => 'application/x-bzip2',
	  'chm' => 'application/vnd.ms-htmlhelp',
	  'doc' => 'application/msword',
	  'eps' => 'application/postscript',
	  'jpg' => 'image/jpeg',
	  'mid' => 'audio/midi',
	  'mp3' => 'audio/mpeg',
	  'ogg' => 'application/ogg',
	  'pdf' => 'application/pdf',
	  'pps' => 'application/vnd.ms-powerpoint',
	  'ppt' => 'application/vnd.ms-powerpoint',
	  'ps' => 'application/postscript',
	  'rtf' => 'application/rtf',
	  'zip' => 'application/zip');
	
	public function listAction() {
		$ordermodel = new ShopSongOrder();
		$albummodel = new ShopAlbum();
		$songmodel = new ShopSong();
		$request = $this->getRequest();

		$id = (int)$request->id;
		if ($id > 0) {
			$orders = $ordermodel->find($id);
			if (count($orders) > 0) {
				$order = $orders->current();
				if ($order->key == $request->key) {
					if ($order->paid == '1') {
						$content = unserialize($order->content);
						$this->view->music = $songmodel->find(array_keys(@$content['music']));
						$this->view->albummusic = $albummodel->find(array_keys(@$content['albummusic']));
						$this->view->score = $songmodel->find(array_keys(@$content['score']));
						$this->view->albumscore = $albummodel->find(array_keys(@$content['albumscore']));
						$this->view->order = $order;
						$this->render('list');
					} else {
						$this->render('notpaid');
					}
				}
			}
		}
	}
	
	public function musicAction() {
		$ordermodel = new ShopSongOrder();
		$songmodel = new ShopSong();
		$request = $this->getRequest();
		
		$this->view->file = NULL;

		$id = (int)$request->id;
		$orderid = (int)$request->orderid;
		if ($id > 0 && $orderid > 0) {
			$orders = $ordermodel->find($orderid);
			$songs = $songmodel->find($id);
			if (count($orders) > 0 && count($songs) > 0) {
				$order = $orders->current();
				$song = $songs->current();
				if ($order->key == $request->key && $order->paid == '1') {
					$content = unserialize($order->content);
					if (isset($content['music'][$id])) {
						$content['music'][$id] += 1;
						$order->content = serialize($content);
						$order->save();
						/*$this->_helper->layout->disableLayout();
						$this->view->file = $song->musicfile;
						$this->view->order = $order;*/
            if (!$this->renderfile($song->musicfile, $order))
              $this->render('filenotfound');
					}
				}
			}
		}
		//$this->render('file');
	}

	public function scoreAction() {
		$ordermodel = new ShopSongOrder();
		$songmodel = new ShopSong();
		$request = $this->getRequest();
		
		$this->view->file = NULL;

		$id = (int)$request->id;
		$orderid = (int)$request->orderid;
		if ($id > 0 && $orderid > 0) {
			$orders = $ordermodel->find($orderid);
			$songs = $songmodel->find($id);
			if (count($orders) > 0 && count($songs) > 0) {
				$order = $orders->current();
				$song = $songs->current();
				if ($order->key == $request->key && $order->paid == '1') {
					$content = unserialize($order->content);
					if (isset($content['score'][$id])) {
						$content['score'][$id] += 1;
						$order->content = serialize($content);
						$order->save();
						$this->_helper->layout->disableLayout();
						$this->view->file = $song->scorefile;
						$this->view->order = $order;
					}
				}
			}
		}
		$this->render('pdf');
	}

	public function albummusicAction() {
		$ordermodel = new ShopSongOrder();
		$albummodel = new ShopAlbum();
		$request = $this->getRequest();
		
		$this->view->file = NULL;

		$id = (int)$request->id;
		$orderid = (int)$request->orderid;
		$part = (int)$request->part;
		if ($id > 0 && $orderid > 0) {
			$orders = $ordermodel->find($orderid);
			$albums = $albummodel->find($id);
			if (count($orders) > 0 && count($albums) > 0) {
				$order = $orders->current();
				$album = $albums->current();
				if ($order->key == $request->key && $order->paid == '1') {
					$content = unserialize($order->content);
					if (isset($content['albummusic'][$id])) {
						$content['albummusic'][$id] += 1;
						$order->content = serialize($content);
						$order->save();
						$parts = explode('|',$album->musicfile);
						$file = @$parts[$part];
						if ($file == '' || !$this->renderfile($file, $order)) {
							$this->render('filenotfound');
						}
					}
				}
			}
		}
		//$this->render('file');
	}

	public function albumscoreAction() {
		$ordermodel = new ShopSongOrder();
		$albummodel = new ShopAlbum();
		$request = $this->getRequest();
		
		$this->view->file = NULL;

		$id = (int)$request->id;
		$orderid = (int)$request->orderid;
		if ($id > 0 && $orderid > 0) {
			$orders = $ordermodel->find($orderid);
			$albums = $albummodel->find($id);
			if (count($orders) > 0 && count($albums) > 0) {
				$order = $orders->current();
				$album = $albums->current();
				if ($order->key == $request->key && $order->paid == '1') {
					$content = unserialize($order->content);
					if (isset($content['albumscore'][$id])) {
						$content['albumscore'][$id] += 1;
						$order->content = serialize($content);
						$order->save();
						$this->_helper->layout->disableLayout();
						$this->view->file = $album->scorefile;
						$this->view->order = $order;
					}
				}
			}
		}
		$this->render('pdf');
	}

	private function renderFile($filepath, $order) {
		$file = $this->view->site->path.'/shop/'.$filepath;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    if (file_exists($file)) {
      $tab = explode('/', $filepath);
      $filename = @$tab[count($tab)-1];
      $tab = explode('.', $filename);
      $ext = @$tab[count($tab)-1];
      if (isset(self::$tabext[$ext])) {
        header ('Content-Type: '.self::$tabext[$ext]);
      } else {
        header ('Content-Type: application/octet-stream');
      }
      header ('Content-Length: '.filesize($file));
      header ('Content-Disposition: attachment; filename='.$filename);
      if ($f = fopen($file, 'rb')) do {
        //set_time_limit(0);
        echo fread($f, 1024*1024);
        //flush();
        //ob_flush();
      } while (!feof($f));
      fclose($f);
      //readfile($file);
      return true;
    }
    return false;
  }

}
?>
