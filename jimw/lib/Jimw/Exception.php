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
class Jimw_Exception extends Exception
{
	/**
	 * contructor
	 */
	public function __construct($code, $message)
	{
		parent::__construct($message, $code);
	}
	/**
	 * Return the error message
	 *
	 */
	public function __toString()
	{
		$str = parent::__toString();
		if (DEBUG_MODE)
			echo $str, "\n";
	}
}
?>