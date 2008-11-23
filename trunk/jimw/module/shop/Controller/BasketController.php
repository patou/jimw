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
		$request = $this->getRequest();
		if (is_array($request->checkscore)) {
			if (!isset($_COOKIE['shopscore'])) $cookie = array();
			else $cookie = explode(',', $_COOKIE['shopscore']);
			foreach ($request->checkscore as $k => $v) {
				if (!in_array($k, $cookie)) $cookie[] = $k;
			}
		}
		setcookie('shopscore', implode(',', $cookie), time()+60*60*24*30);
		$this->_redirect($this->view->url(array('module' => 'shop', 'controller' => 'basket', 'action' => 'show'), 'format', true));
	}
	
	public function showAction() {
		$songmodel = new ShopSong();
		$songs = NULL;
		if (isset($_COOKIE['shopscore'])) {
		  $tab = explode(',', $_COOKIE['shopscore']);
		  if (count($tab) > 0) {
		    $songs = $songmodel->find($tab);
		  }
		}
		$this->view->songs = $songs;
		$this->render();
	}

	public function buyAction() {
		$songmodel = new ShopSong();
		$songs = NULL;
		if (isset($_COOKIE['shopscore'])) {
		  $tab = explode(',', $_COOKIE['shopscore']);
		  if (count($tab) > 0) {
		    $songs = $songmodel->find($tab);
		  }
		}
		$this->view->songs = $songs;
		$this->render();
	}	

	public function confirmAction() {
		$songmodel = new ShopSong();
		$songs = NULL;
		if (isset($_COOKIE['shopscore'])) {
		  $tab = explode(',', $_COOKIE['shopscore']);
		  if (count($tab) > 0) {
		    $songs = $songmodel->find($tab);
		  }
		}
		$this->view->songs = $songs;
		
		// Verification
		$request = $this->getRequest();
		if (!isset($request->name) || $request->name == '' ||
		    !isset($request->firstname) || $request->firstname == '' ||
			!isset($request->email) || $request->email == '' || is_null($songs)) $this->render('buy');
		else {
			$sum = 0;
			foreach ($songs as $song) $sum += $song->scoreprice;
			$ordermodel = new ShopSongOrder();
			$item = $ordermodel->fetchNew();
			$item->name = $request->name;
			$item->firstname = $request->firstname;
			$item->email = $request->email;
			$item->date = Zend_Date::now()->getIso();
			$item->musiclist = '';
			$item->scorelist = $_COOKIE['shopscore'];
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
				$order->paid = '1';
				$order->save();
				$this->view->order = $order;
				$this->render("sendlink");
			}
		}
	}
	
}
?>