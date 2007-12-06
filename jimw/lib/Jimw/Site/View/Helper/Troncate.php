<?php

class Jimw_Site_View_Helper_Troncate {
	public function troncate($message, $length = 50) {
		if (strlen($message) > $length) {
			return substr($message, 0, $length) . ' ...';
		}
		return $message;
	}
}

?>
