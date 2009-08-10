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
	public function preDispatch() {
		parent::preDispatch();
		$this->view->paymentmode = 'sogenactif';
		$this->view->websalto = true;
	}
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
		/*$model = new Jimw_Site_Module();
		$module = $model->fetchRow(array('module_path = ?' => 'shop'));
		$tab = unserialize($module->config);
		print_r($module->config->config);
		$tab['paymentmode'] = 'sogenactif';
		$module->config = serialize($tab);*/
		
		$this->_list();
		
		// Verification
		$request = $this->getRequest();
		if (!isset($request->name) || $request->name == '' ||
		    !isset($request->firstname) || $request->firstname == '' ||
		    !isset($request->email) || $request->email == '') $this->render('buy');
		elseif ($this->view->websalto)
			$this->_forward('createdownloadbasket', 'websalto');
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
			$item->date = $item->getFormatedDate(Zend_Date::now());
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
	
	public function autoresponseAction() {
		$message="message=$HTTP_POST_VARS[DATA]";
		$pathfile="pathfile=/home/sogenactif/param/pathfile";
		$path_bin = "/home/sogenactif/bin/response";
		$result=exec("$path_bin $pathfile $message");
	//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
	//		- code=0	: la fonction retourne les données de la transaction dans les variables v1, v2, ...
	//				: Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
	//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error
		$tableau = explode ("!", $result);

		$code = $tableau[1];
		$error = $tableau[2];
		$merchant_id = $tableau[3];
		$merchant_country = $tableau[4];
		$amount = $tableau[5];
		$transaction_id = $tableau[6];
		$payment_means = $tableau[7];
		$transmission_date= $tableau[8];
		$payment_time = $tableau[9];
		$payment_date = $tableau[10];
		$response_code = $tableau[11];
		$payment_certificate = $tableau[12];
		$authorisation_id = $tableau[13];
		$currency_code = $tableau[14];
		$card_number = $tableau[15];
		$cvv_flag = $tableau[16];
		$cvv_response_code = $tableau[17];
		$bank_response_code = $tableau[18];
		$complementary_code = $tableau[19];
		$complementary_info= $tableau[20];
		$return_context = $tableau[21];
		$caddie = $tableau[22];
		$receipt_complement = $tableau[23];
		$merchant_language = $tableau[24];
		$language = $tableau[25];
		$customer_id = $tableau[26];
		$order_id = $tableau[27];
		$customer_email = $tableau[28];
		$customer_ip_address = $tableau[29];
		$capture_day = $tableau[30];
		$capture_mode = $tableau[31];
		$data = $tableau[32];

		$logfile="/home/sogenactif/autoresponse.log";
		$fp=fopen($logfile, "a");
	  if (( $code == "" ) && ( $error == "" ) )
	 	{
	  	fwrite($fp, "erreur appel response\n");
	  	print ("executable response non trouve $path_bin\n");
	 	}
		
		//	Erreur, sauvegarde le message d'erreur
		
		else if ( $code != 0 ){
	        fwrite($fp, " API call error.\n");
	        fwrite($fp, "Error message :  $error\n");
	 	}
		else {
		
		// OK, Sauvegarde des champs de la réponse
		
		fwrite( $fp, "merchant_id : $merchant_id\n");
		fwrite( $fp, "merchant_country : $merchant_country\n");
		fwrite( $fp, "amount : $amount\n");
		fwrite( $fp, "transaction_id : $transaction_id\n");
		fwrite( $fp, "transmission_date: $transmission_date\n");
		fwrite( $fp, "payment_means: $payment_means\n");
		fwrite( $fp, "payment_time : $payment_time\n");
		fwrite( $fp, "payment_date : $payment_date\n");
		fwrite( $fp, "response_code : $response_code\n");
		fwrite( $fp, "payment_certificate : $payment_certificate\n");
		fwrite( $fp, "authorisation_id : $authorisation_id\n");
		fwrite( $fp, "currency_code : $currency_code\n");
		fwrite( $fp, "card_number : $card_number\n");
		fwrite( $fp, "cvv_flag: $cvv_flag\n");
		fwrite( $fp, "cvv_response_code: $cvv_response_code\n");
		fwrite( $fp, "bank_response_code: $bank_response_code\n");
		fwrite( $fp, "complementary_code: $complementary_code\n");
		fwrite( $fp, "complementary_info: $complementary_info\n");
		fwrite( $fp, "return_context: $return_context\n");
		fwrite( $fp, "caddie : $caddie\n");
		fwrite( $fp, "receipt_complement: $receipt_complement\n");
		fwrite( $fp, "merchant_language: $merchant_language\n");
		fwrite( $fp, "language: $language\n");
		fwrite( $fp, "customer_id: $customer_id\n");
		fwrite( $fp, "order_id: $order_id\n");
		fwrite( $fp, "customer_email: $customer_email\n");
		fwrite( $fp, "customer_ip_address: $customer_ip_address\n");
		fwrite( $fp, "capture_day: $capture_day\n");
		fwrite( $fp, "capture_mode: $capture_mode\n");
		fwrite( $fp, "data: $data\n");
		fwrite( $fp, "-------------------------------------------\n");
		}
	
		fclose ($fp);
	}
	
}
?>