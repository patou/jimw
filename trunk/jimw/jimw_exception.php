<?php
/**
 * JIMW_EXCEPTION
 *
 */
class jimw_exception extends Exception
{
	public function __construct($code, $message)
	{
		parent::__construct($message, $code);
	}
}
?>