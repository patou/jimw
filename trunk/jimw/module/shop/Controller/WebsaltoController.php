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
class Shop_WebsaltoController extends Jimw_Module_Action
{
	/*public function preDispatch() {
		parent::preDispatch();
		$this->view->paymentmode = 'sogenactif';
		//$this->view->websalto = true;
	}*/
	
	public function createdownloadbasketAction() {
		$basket = new Zend_Session_Namespace('Basket');
		$this->view->basket = $basket;
		$albummodel = new ShopAlbum();
		$songmodel = new ShopSong();
		$references = array();
		$content = array();
		$sum = 0;
		
		$music = array();
		$list = array();
		if (isset($basket->music) && is_array($basket->music) && count($basket->music) > 0) {
			$music = $songmodel->find(array_keys($basket->music));
			foreach ($music as $item) {
				$references[] = $item->musicref;
				$sum += $item->musicprice;
				$content['music'][$item->id] = 0;
			}
		}

		$albummusic = array();
		if (isset($basket->albummusic) && is_array($basket->albummusic) && count($basket->albummusic) > 0) {
			$albummusic = $albummodel->find(array_keys($basket->albummusic));
			foreach ($albummusic as $item) {
				$references[] = $item->musicref;
				$sum += $item->musicprice;
				$content['albummusic'][$item->id] = 0;
			}
		}
		
		$score = array();
		if (isset($basket->score) && is_array($basket->score) && count($basket->score) > 0) {
			$score = $songmodel->find(array_keys($basket->score));
			foreach ($score as $item) {
				$references[] = $item->scoreref;
				$sum += $item->scoreprice;
				$content['score'][$item->id] = 0;
			}
		}

		$albumscore = array();
		if (isset($basket->albumscore) && is_array($basket->albumscore) && count($basket->albumscore) > 0) {
			$albumscore = $albummodel->find(array_keys($basket->albumscore));
			foreach ($albumscore as $item) {
				$references[] = $item->scoreref;
				$sum += $item->scoreprice;
				$content['albumscore'][$item->id] = 0;
			}
		}
		
		// Verification
		if (count($references) > 0) {
			$request = $this->getRequest();
			$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=AjoutePanier&ARGUMENTS=-N0,-A'.$references[0].',-A');
			if (preg_match('/<basket id="(\d+)" \/>/', $page, $matches)) {
				$basketid = $matches[1];
				for ($i=1; $i<count($references); $i++) {
					$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=AjoutePanier&ARGUMENTS=-N'.$basketid.',-A'.$references[$i].',-AOui');
				}
				// Login
				$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=SaisieInscriptionOUVRAGE&ARGUMENTS=-N'.$basketid);
				$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=Aff_Inscription_Ouvrage&ID_SALTO='.$basketid.'&Essais_Login=1&code_acces=telec&mot_passe=telec');
				// Adresse de facturation
				$civilite = $request->civilite;
				$nom = $request->name;
				$prenom = $request->firstname;
				$adresse = $request->adresse;
				$codepostal = $request->codepostal;
				$ville = $request->ville;
				$pays = $request->pays;
				$email = $request->email;
				$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=Rec_Ouvrages_Form&ID_SALTO='.$basketid.'&type_formulaire=Facturation&Activite=&appel='.urlencode($civilite).'&nom='.urlencode($nom).'&prenom='.urlencode($prenom).'&fonction=&organisation=&adresse_1='.urlencode($adresse).'&adresse_2=&adresse_3=&codepostal='.urlencode($codepostal).'&ville='.urlencode($ville).'&pays='.urlencode($pays).'&telephone=&fax=&email='.urlencode($email).'&mode_paiement=Carte&Valider=Valider');
				// Adresse de livraison
				$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=Rec_Ouvrages_Form&ID_SALTO='.$basketid.'&type_formulaire=Livraison&Activite=&appel=&nom=TELECHARGEMENT&prenom=MEJ&fonction=&organisation=TELECHARGEMENT&adresse_1='.urlencode('28 RUE MOLITOR').'&adresse_2=&adresse_3=&codepostal=75016&ville=PARIS&pays=France&telephone=&fax=&email='.urlencode('adminweb.mej@gmail.com').'&envoi=France&Valider=Valider');

				// Insertion dans la base
				$ordermodel = new ShopSongOrder();
				$item = $ordermodel->fetchNew();
				$item->name = $request->name;
				$item->firstname = $request->firstname;
				$item->email = $request->email;
				$item->date = $item->getFormatedDate(Zend_Date::now());
				$item->content = serialize($content);
				$item->price = $sum;
				$item->paid = '0';
				$item->key = $basketid;
				$id = $item->save();
				//$id = Jimw_Db_Table::getDefaultAdapter()->lastInsertId();
				//$this->view->item = $item;
				//$this->view->orderid = $id;
				//$this->render();

				$page = file_get_contents('http://office.mej.fr:8080/scripts/mgrqcgi.exe?APPNAME=WEB_SALTO&PRGNAME=Continuer_Ouvrage&ARGUMENTS=-N'.$basketid.',-AFin,-A');
				if (preg_match('/NAME=DATA VALUE="([a-zA-Z0-9]+)"/', $page, $matches)) {
				  $url = "https://paiement.sogenactif.com/cgis-payment-sogenactif/prod/callpayment?DATA=".$matches[1]."&".$request->paymentmode.".x=10&".$request->paymentmode.".y=5";
				  header ('Location: '.$url);
				}
			}
		}
		$this->render('basketerror');
	}
	
	public function downloadAction() {
		$temp = $this->getRequest()->basketid;
		if (preg_match('/^\d+-WEB-(\d+)$/', $temp, $matches)) $basketid = $matches[1];
		else $basketid = (int)$temp;
		$name = $this->getRequest()->name;
		$page = file_get_contents('http://office.mej.fr:8081/websalto/basket_content.php?basket_id='.$basketid);
		if ($page != '') {
			// La référence existe
			$xmlarticles = simplexml_load_string($page);
			$albummodel = new ShopAlbum();
			$songmodel = new ShopSong();
			$ordermodel = new ShopSongOrder();
			$order = $ordermodel->fetchRow($ordermodel->select()->where('shopsongorder_key = ?', $basketid));
			if ($order) {
				// Commande trouvée
				if ($xmlarticles['paid'] == '1') {
					$order->paid = '1';
					$order->save();
				}
				// Saisie du nom de famille
				if (strtolower($order->name) != strtolower($name)) {
					$this->view->basketid = $basketid;
					$this->render('entername');
				} else {
					$this->_forward('list', 'download', null, array('id' => $order->id, 'key' => $order->key));
				}
				//$content = array();
				/*if (count(unserialize($order->content)) == 0) {
					// Articles pas encore référencés
				}
				$order->save();*/
			} else {
				$this->render('notfound');
			}
		} else {
			$this->render('notfound');
		}
		
	}
	
	private function _getIdList($rowset) {
		$tab = array();
		foreach ($rowset as $row) $tab[] = $row->id;
		return implode(',', $tab);
	}

}
?>