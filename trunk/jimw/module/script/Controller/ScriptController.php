<?php
/**
 * ScriptController
 * Module to create web form
 * @author	    Patou
 * @category   Jimw_Form
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: FormController.php 157 2008-02-07 13:19:58Z patou.de.saint.steban $
 */


class Script_ScriptController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
	    $req = $this->getRequest();
	    $tree = $req->getTree();
		$form_file = $tree->param->script_file;
		$this->renderScript($form_file);
	}
}
