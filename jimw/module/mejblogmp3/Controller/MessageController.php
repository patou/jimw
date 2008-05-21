<?php
/**
 * NewsController
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/Mejblogmp3Message.php');
include_once('Model/Mejblogmp3Message/Row.php');
include_once('Model/Mejblogmp3Message/Rowset.php');
include_once('Model/Mejblogmp3Comment.php');
class Mejblogmp3_MessageController extends Jimw_Module_Action
{
  public function addAction ()
  {
    $request = $this->_request;
    $tree = $request->getTree();
    $mp3 = $_FILES['mfilemp3'];
    $pdf = $_FILES['mfilepdf'];
    $dir = rtrim('./' . $tree->site->path, '/') . '/blogmp3';
	if ($mp3['type'] == 'audio/mpeg' && $mp3['error'] == 0 && $request->mname != "" && $request->mfirstname != ""
	    && $request->mmessage != "" && $request->mtitle != "" && $request->memail != "") {
	  $messages = new Mejblogmp3Message();
      $message = $messages->fetchNew();
      $message->title = $request->mtitle;
      $message->tree_parentid = $tree->id;
      $message->content = $request->mmessage;
      $message->name = $request->mname;
      $message->firstname = $request->mfirstname;
      $message->email = $request->memail;
      $message->phone = $request->mphone;
      $message->published = false;
      $message->date = Zend_Date::now()->getIso();
      $newid = $message->save();
	  move_uploaded_file($mp3['tmp_name'], $dir.'/'.$newid.'.mp3');
	  if ($pdf['type'] == 'application/pdf' && $pdf['error'] == 0) {
	  	  move_uploaded_file($pdf['tmp_name'], $dir.'/'.$newid.'.pdf');
	  }
	  //TODO: mail();
      $this->render('success');
	} else {
	  $this->render('error');
	}
  }
}
?>