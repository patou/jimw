<?php
/**
 * Jimw_Site_View_Helper_Troncate
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Jimw_Site_View_Helper_Troncate {
	public function troncate($message, $length = 50) {
		if (strlen($message) > $length) {
			return substr($message, 0, $length) . '...';
		}
		return $message;
	}
}

?>
