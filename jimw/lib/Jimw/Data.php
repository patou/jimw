<?php
/**
 * JIMW
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw_Data
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
/**
  * Data base class
  * A Data class is a container of data with type (see children class)
  * @abstract Abstract class
  * @author Patou
  */
abstract class Jimw_Data
{
	/**
	 * Name of the data (same name as column in the database)
	 * This name can be a table (liste of column name in the database)
	 * @var string or array
	 */
	private $name = "";
	/**
	 * Array of option for the data
	 * This option are specific for the data type
	 * As exemple (max size of the data)
	 *
	 * @var array
	 */
	private $options = array();
		
	/**
	 * Construct a new data
	 *
	 * @param string or data $name The column name in the database (may be a list)
	 * @param array $options Option for data
	 */
	public function __construct ($name, array $options)
	{
		$this->name = $name;
		$this->options = $options;
	}
	
	/**
	 * Verify if the data is good
	 *
	 * @param $data
	 * @return boolean If the data is good or a exception error
	 * @exception Jimw_Data_Exception If the data isn't valid 
	 */
	public abstract function verifyData ($data);
	
	/**
	 * Set the data name
	 *
	 * @param string or array $name
	 */
	public function setName ($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Get the data column name
	 *
	 * @return sring or array
	 */
	public function getName ()
	{
		return $this->name;
	}
	
	/**
	 * Set the option array
	 *
	 * @param array $option
	 */
	public function setOption (array $options)
	{
		$this->options = $options;
	}
	
	/**
	 * Get the array options
	 *
	 * @return unknown
	 */
	public function getOptions ()
	{
		return $this->options;
	}
	
	/**
	 * Get the option
	 * 
	 * @param string The option name to search
	 * @return mixed The option value (if the options didn't exist, a empty value is return)
	 */
	public function getOption ($name)
	{
		return $this->options[$name];
	}
	
	/**
	 * Add a new option value in the array of options
	 * If the option allready exsist, she is replaced
	 *
	 * @param string $name Option name
	 * @param $value Option Value
	 */
	public function addOption ($name, $value)
	{
		$this->options[$name] = $value;
	}
	
	protected function __get ($name)
	{
		return $this->getOption($name);
	}
	
	protected function __isset ($name)
	{
		return isset($this->options[$name]);
	}
}
?>