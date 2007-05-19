<?php
/**
 * Class interface for a Module
 */
abstract class jimw_module
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