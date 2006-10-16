<?php
/**
 * JIMW_LANG
 * @package JIMW
 */
class jimw_lang implements ArrayAccess
{
	public  $_l = array();
	private $_f = array();
	public  $lang;
	public 	$defaultlang;


	public 	function __construct($lang, $defaultlang = 'en')
	{
		$this->setlang($lang, $defaultlang);
		$this->load('common');
	}

	public 	function setlang($lang, $defaultlang = 'en')
	{
		$this->lang = $lang;
		$this->defaultlang = $defaultlang;
	}

	public 	function load($name, $lang = '')
	{
		if (empty($lang))
			$lang = $this->lang;
		$this->_f[] = $name;
		$this->_load($name, $lang);
	}

	public 	function __get($name)
	{
		return $this->_getVal($name);
	}

	public 	function __set($name, $value)
	{

	}

	public 	function __call($name, $args)
	{
		array_unshift($args, $this->_getVal($name));
		return call_user_func_array('sprintf', $args);
	}

	public 	function offsetExists($name) {
		return ($this->_getVal($name) == '');
	}

	public 	function offsetGet($name) {
		return $this->_getVal($name);
	}

	public 	function offsetSet($name, $val) {
		$this->__set($name, $val);
	}

	public 	function offsetUnset($name) {
		$this->__set($name, '');
	}


	private function _getVal($name)
	{
		if ($val = $this->_searchVal($this->_l[$this->lang], $name))
			return $val;
		elseif ($val = $this->_tryLoad($name, $this->defaultlang))
			return $val;
		else
		{
			return $val = $this->_searchVal($this->_l, $name);
		}
	}

	private function _load($name, $l)
	{
		include('jimw/lang/'.$l.'/'.$name.'.php');
		if (!isset($this->_l[$l]))
		 	$this->_l[$l] = array();
		$this->_l[$l][$name] = $lang;
	}

	private function _tryLoad($name, $lang)
	{
		if (!isset($this->_l[$lang]))
		 	$this->_l[$lang] = array();
		foreach ($this->_f as $module)
		{
			if (!isset($this->_l[$lang][$module]))
				$this->_load($module, $lang);
			$v = $this->_searchVal($this->_l[$lang][$module], $name);
			if ($v)
				return $v;
		}
		return false;
	}

	private function _searchVal($tab, $name)
	{
		foreach ($tab as $key => $val)
		{
			if (is_array($val))
			{
				$v = $this->_searchVal($tab[$key], $name);
				if ($v)
					return $v;
			}
			if ($key == $name)
				return $val;
		}
		return false;
	}
}

?>