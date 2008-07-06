<?php
/**
 * Jimw_Admin_Element_Base
 *
 * @author	   Logisim
 * @category   Jimw_Admin_Element
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
 
abstract class Jimw_Admin_Element_Base
{
	public function urlEncode($str) {
		return str_replace(array('%2F', '%3D', '%3A', '%3B', '%40', '%26', '%23', '%24', '%3F', '%2C', '%2B'), array('/', '=', ':', ';', '@', '&', '#', '$', '?', '+', ','), rawurlencode(utf8_encode($str)));
	}
}