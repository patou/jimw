<?php
/**
 * JIMW_EXCEPTION
 *
 */
class jimw_exception extends Exception
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