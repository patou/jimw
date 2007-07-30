<?php
/**
 * urlEncode.php
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw_Core
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Admin_View_Helper_UrlEncode
{
	public function urlEncode($str) {
		return str_replace(array('%2F', '%3D', '%3A', '%3B', '%40', '%26', '%23', '%24', '%3F', '%2C', '%2B'), array('/', '=', ':', ';', '@', '&', '#', '$', '?', '+', ','), rawurlencode(utf8_encode($str)));
	}
}