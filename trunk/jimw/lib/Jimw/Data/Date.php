<?php
/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw_Data_String
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
/**
  * Data String class
  * A Data class is a container of simple string
  * @abstract class
  * @author Patou
  */
class Jimw_Data_Date extends Jimw_Data
{
	/**
	 * The data
	 * @var string
	 */
	private $data = "";
		
	/**
	 * Construct a new data
	 * new Data_Date('birthday', array('old_greater' => value))
	 *
	 * @param string or data $name The column name in the database (may be a list)
	 * @param array $options Option for data
	 */
	public function __construct ($name, array $options)
	{
		parent::__construct($name,$options);
	}
	
	/**
	 * Verify if the data is good
	 *
	 * @param $data
	 * @return boolean If the data is good or a exception error
	 * @exception Jimw_Data_Exception If the data isn't valid 
	 */
	public function verifyData ($data)
	{
		
		// @todo
		if (!Zend_Validate_Date::isValid($data))
			return false;
		if (isset($this->old_greater) )
			return false;
			
		
		return true;
	}
}
?>