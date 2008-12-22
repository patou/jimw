<?php
/**
 * BasketController
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
class Shop_BasketController extends Jimw_Module_Action
{
	public function addAction() {
	  $basket = new Zend_Session_Namespace('Basket');
		$request = $this->getRequest();
		if (!isset($basket->score)) $basket->score = array();
		if (!isset($basket->music)) $basket->music = array();
		if (!isset($basket->albumscore)) $basket->albumscore = array();
		if (!isset($basket->albummusic)) $basket->albummusic = array();

		if (is_array($request->checkscore)) {
			foreach ($request->checkscore as $k => $v) {
				if (!isset($basket->score[$k])) $basket->score[$k] = 1;
			}
		}
		if (is_array($request->checkmusic)) {
			foreach ($request->checkmusic as $k => $v) {
				if (!isset($basket->music[$k])) $basket->music[$k] = 1;
			}
		}

		if (is_array($request->checkalbumscore)) {
			foreach ($request->checkalbumscore as $k => $v) {
				if (!isset($basket->albumscore[$k])) $basket->albumscore[$k] = 1;
				$songmodel = new ShopSong();
				$songs = $songmodel->fetchAll(array('shopalbum_id = ?' => $k));
				foreach ($songs as $song) {
					if (isset($basket->score[$song->id])) unset($basket->score[$song->id]);
				}
			}
		}

		if (is_array($request->checkalbummusic)) {
			foreach ($request->checkalbummusic as $k => $v) {
				if (!isset($basket->albummusic[$k])) $basket->albummusic[$k] = 1;
				$songmodel = new ShopSong();
				$songs = $songmodel->fetchAll(array('shopalbum_id = ?' => $k));
				foreach ($songs as $song) {
					if (isset($basket->music[$song->id])) unset($basket->music[$song->id]);
				}
			}
		}
		$this->_redirect($this->view->url(array('module' => 'shop', 'controller' => 'basket', 'action' => 'show'), 'format', true));
	}
	
	public function deletescoreAction() {
		$basket = new Zend_Session_Namespace('Basket');
		$request = $this->getRequest();
		if (isset($basket->score[$request->id])) unset ($basket->score[$request->id]);
		$this->_redirect($this->view->url(array('module' => 'shop', 'controller' => 'basket', 'action' => 'show'), 'format', true));
	}
	
	public function deletealbumscoreAction() {
		$basket = new Zend_Session_Namespace('Basket');
		$request = $this->getRequest();
		if (isset($basket->albumscore[$request->id])) unset ($basket->albumscore[$request->id]);
		$this->_redirect($this->view->url(array('module' => 'shop', 'controller' => 'basket', 'action' => 'show'), 'format', true));
	}
	
	public function deletemusicAction() {
		$basket = new Zend_Session_Namespace('Basket');
		$request = $this->getRequest();
		if (isset($basket->music[$request->id])) unset ($basket->music[$request->id]);
		$this->_redirect($this->view->url(array('module' => 'shop', 'controller' => 'basket', 'action' => 'show'), 'format', true));
	}
	
	public function deletealbummusicAction() {
		$basket = new Zend_Session_Namespace('Basket');
		$request = $this->getRequest();
		if (isset($basket->albummusic[$request->id])) unset ($basket->albummusic[$request->id]);
		$this->_redirect($this->view->url(array('module' => 'shop', 'controller' => 'basket', 'action' => 'show'), 'format', true));
	}
	
	private function _list() {
		$basket = new Zend_Session_Namespace('Basket');
		$this->view->basket = $basket;
		$albummodel = new ShopAlbum();
		$songmodel = new ShopSong();
		
		$music = array();
		if (isset($basket->music) && is_array($basket->music) && count($basket->music) > 0) {
		  $music = $songmodel->find(array_keys($basket->music));
		}
		$this->view->music = $music;

		$albummusic = array();
		if (isset($basket->albummusic) && is_array($basket->albummusic) && count($basket->albummusic) > 0) {
		  $albummusic = $albummodel->find(array_keys($basket->albummusic));
		}
		$this->view->albummusic = $albummusic;
		
		$score = array();
		if (isset($basket->score) && is_array($basket->score) && count($basket->score) > 0) {
		  $score = $songmodel->find(array_keys($basket->score));
		}
		$this->view->score = $score;

		$albumscore = array();
		if (isset($basket->albumscore) && is_array($basket->albumscore) && count($basket->albumscore) > 0) {
		  $albumscore = $albummodel->find(array_keys($basket->albumscore));
		}
		$this->view->albumscore = $albumscore;
	}
	
	public function showAction() {
		$this->_list();
		$this->render();
	}

	public function buyAction() {
		$this->_list();
		$this->render();
	}	
	
	private function _getIdList($rowset) {
		$tab = array();
		foreach ($rowset as $row) $tab[] = $row->id;
		return implode(',', $tab);
	}

	public function confirmAction() {
		$this->_list();
		
		// Verification
		$request = $this->getRequest();
		if (!isset($request->name) || $request->name == '' ||
		    !isset($request->firstname) || $request->firstname == '' ||
			  !isset($request->email) || $request->email == '') $this->render('buy');
		else {
			$sum = 0;
			foreach ($this->view->music as $song) $sum += $song->musicprice;
			foreach ($this->view->albummusic as $album) $sum += $album->musicprice;
			foreach ($this->view->score as $score) $sum += $score->scoreprice;
			foreach ($this->view->albumscore as $album) $sum += $album->scoreprice;
			$ordermodel = new ShopSongOrder();
			$item = $ordermodel->fetchNew();
			$item->name = $request->name;
			$item->firstname = $request->firstname;
			$item->email = $request->email;
			$item->date = Zend_Date::now()->getIso();
			$item->musiclist = $this->_getIdList($this->view->music);
			$item->albummusiclist = $this->_getIdList($this->view->albummusic);
			$item->scorelist = $this->_getIdList($this->view->score);
			$item->albumscorelist = $this->_getIdList($this->view->albumscore);
			$item->price = $sum;
			$item->paid = '0';
			$item->key = md5($request->name.$request->firstname.$request->email.microtime());
			$item->save();
			$id = Jimw_Db_Table::getDefaultAdapter()->lastInsertId();
			$this->view->item = $item;
			$this->view->orderid = $id;
			$this->render();
		}
	}
	
	public function paypalipnAction() {
		$request = $this->getRequest();
		mail ("Simon LEPRINCE <simon@leprince.net>",
		      "[JIMW PayPal IPN] Send POST",
		      print_r($_POST, true).
		      print_r($_REQUEST, true).
		      print_r($_SERVER, true),
		      "From: contact@jimw.fr\nX-Mailer: JIMW Mail");
		if (isset($request->item_number) && is_numeric($request->item_number)) {
			$ordermodel = new ShopSongOrder();
			$orders = $ordermodel->find($request->item_number);
			if (count($orders) == 1) {
				$order = $orders->current();

	/*$post = @$_POST;
	$post['cmd'] = '_notify-validate';
	$ctx = stream_context_create(array('https' => array('method' => 'post', 'content' => $post)));
	$fp = @fopen('https://www.sandbox.paypal.com/cgi-bin/webscr', 'rb', false, $ctx);
	if (!$fp) throw new Exception("Problem with $url, $php_errormsg");
	$response = @stream_get_contents($fp);
	if ($response === false) throw new Exception("Problem reading data from $url, $php_errormsg");
	mail ("Simon LEPRINCE <simon@leprince.net>",
	      "[JIMW PayPal IPN] Response",
	      $response,
	      "From: contact@jimw.fr\nX-Mailer: JIMW Mail");*/

				$order->paid = '1';
				$order->save();
				$this->view->order = $order;
				$this->render('sendlink');
			}
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
}
?>