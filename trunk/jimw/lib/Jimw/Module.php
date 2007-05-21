<?php
/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Data.php 2797 2007-01-16 01:35:30Z patou $
 */
/**
 * Class interface for a Module
 */
abstract class Jimw_Module
{
	public $id;
	public $config;

	protected function __construct( $id, $config)
	{
		$this->id = $id;
		$this->config = $config;
	}

	abstract  function sessionStart()
	{

	}

}

interface module
{
	public function onLoad();
	public function onInstall();
	public function onEnabled();
	public function onDesabled();
	public function onUninstall();
}

interface module_block
{
	public function getBlock();
	public function setConfigBlock();
	public function getBlockAdmin();
	public function setBlockConfig();
}

interface module_search
{
	public function getSearch();
	public function getSearchList();
}

interface module_admin
{
	public function getAdminName();
	public function getAdmin();
	public function getAdminConf();
	public function setAdminConf();
}
interface module_content
{
	public function onCreate();
	public function onPublish();
	public function onEdit();
	public function onDisplay();
}
interface module_type
{
	public function getTypes();
	public function getTypeField();
	public function getTypeConfig();
	public function getTypeDisplay();
}
?>