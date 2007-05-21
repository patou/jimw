<?php
/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Data.php 2797 2007-01-16 01:35:30Z patou $
 */
class Jimw_Version
{
	/** @var string Product */
	public $product = 'JIMW';
	/** @var string Product long */
	public $product_long = 'Just Immagine My Website';
	/** @var string Compagny name */
	public $company = 'JesusWay.fr';
	/** @var int Major version number */
	public $major_version = 2;
	/** @var int Minor version number */
	public $minor_version = 0;
	/** @var int Develper version number */
	public $dev_version = 'Alpha 1';
	/** @var int Revision version number */
	public $revision = '$Revision: $';

	/**
	 * Return a long string of the version
	 *
	 * @return string
	 */
	public function getLongVersion()
	{
		return $this->product . ' ' . $this->major_version . '.' . $this->minor_version . ' ' . $this->dev_version;
	}
	
	/**
	 * Return a string of the version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->major_version . '.' . $this->minor_version;
	}
	
	/**
	 * Return the copyright string
	 *
	 * @return string
	 */
	public function getCopyright()
	{
		return 'Copyright (C) 2006 ' . $this->company . '. All rights Reserved.';
	}
	
	/**
	 * Return the php version
	 *
	 * @return string
	 */
	public function getPhpVersion()
	{
		return phpversion();
	}
	
	/**
	 * Test if an php extention is loaded
	 *
	 * @return bool
	 */
	public function asPhpExtention($ext)
	{
		return extension_loaded($ext);
	}
}
?>