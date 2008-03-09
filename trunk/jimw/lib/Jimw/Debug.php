<?php

/**
 *
 */
class Jimw_Debug extends Zend_Debug {
	static public $output = '';
	public static function initDebug ()
	{
		$errorhandler = array (
			new Jimw_Debug (),
			'errorHandlerCallback'
		);
		
		$displayEnd = array(
			new Jimw_Debug (),
			'displayEnd'
		);
		
		register_shutdown_function($displayEnd);
		//set_error_handler($errorhandler);
	}

	/**
	 * 
	 * 
	 */
	public function displayEnd() {
		echo self::$output;
	}
		/**
	 * callback method for PHP errorhandling
	 * 
	 * @TODO implement more errorlevels
	 */
	public function errorHandlerCallback () {
		$details = func_get_args();
		$details[1] = str_replace("'", '"', $details[1]);
		$details[1] = str_replace('href="function.', 'target="_blank" href="http://www.php.net/', $details[1]);
		
		$color = 'orange';
		/* determine error level */
		switch ($details[0]) {
			case 2:
				$errorlevel = 'warning';
				$color = 'red';
				break;
			case 8:
				$errorlevel = 'notice';
				$color = 'orange';
				break;
			case 2048:
				$errorlevel = 'suggestion';
				$color = 'orange';
				break;
		}

		$fullTraceback = $details[2] . ' on line ' . $details[3];
		$file = $this->cropScriptPath($details[2]);
		
		$infos = '<strong>';
		$infos .= 'PHP ' . strtoupper($errorlevel) . '</strong>';
		$infos .= $details[1] . '<br /><acronym class="backtrace" title="' . $fullTraceback . '">';
		$infos .= $file . ' on line ';
		$infos .= $details[3] . '</span>';		
		
		self::display($infos, $details[1], $color);
	}
	
	public static function dump($var, $label = null, $echo = true)
	{
		return self::display(parent::dump($var, null, false), $label, 'yellow', $echo);
	}
	
	public static function display ($message, $title = '', $color = '#00E600',$echo = true)
	{
		static $id = 0;
		$div_id = 'debug'.$id++;
		if (!JIMW_DEBUG_MODE) {
			$output = "\n<!--\n";
		}
		else {
			if (empty($title)) {
				$pos = strpos($message, "\n");
				if ($pos === false || $pos < 1) { 
					$title = $message;
					$message = '';
				}
				else {
					if ($pos > 80)
						$pos = 80;
					$title = substr($message, 0, $pos) . ' ...';
					$message = substr($message, strlen($title));
				}
			}
			$output = '<div style="border: 1px solid ' .$color . ';
        	border-left: 5px solid ' .$color . ';
        	background-color: white;
        	padding: 3px;
        	margin: 5px 3px;">' . "\n";
			$output .= '<a href="" onclick="el = document.getElementById(\''.$div_id.'\'); el.style.display = (el.style.display == \'none\') ? \'block\' : \'none\'; return false;">';
			$output .= $title;
			$output .= '</a><div id="'.$div_id.'" style="display:none;">';
		}
		$output .= $message . "\n";
		$output .= self::getTraceback();
		if (!JIMW_DEBUG_MODE) {
			 $output .= "\n-->\n";
		}
		else {
			$output .= '</div></div>' . "\n";
		}
		if ($echo) {
			self::$output .= $output;
        }
        return $output;
	}
	
	public static function display_exception (Exception $e, $echo = true)
	{
		$title = 'Exception ['. get_class($e). '-'. $e->getCode(). '] : ' . $e->getMessage();
		$file = $e->getFile();
		$output = $title;
		$output .= ' in <acronym class="backtrace" title="' . $file . ' on line ' . $e->getLine() . '">'. self::cropScriptPath($e->getFile()) . ' on line ' . $e->getLine() . "\n<br />";
		$stack = $e->getTrace();
		//Zend_Debug::dump($stack);
		foreach ($stack as $item) {
			if ($item && isset($item['file']) && $item['line'])
				$output .= '<acronym class="backtrace" title="' . $item['file'] . ' on line ' . $item['line'] . ' ' .(isset($item['class']) ? $item['class']. $item['type'] : '').  $item['function']. '()">'. self::cropScriptPath($item['file']) . ' on line ' . $item['line'] . ' ' .(isset($item['class']) ? $item['class']. $item['type'] : '').  $item['function']. "()\n<br />";
		}
        return self::display($output, $title, 'red', $echo);
	}
	
	public static function profile_db($db, $title = '',$echo = true)
	{
		if ($db && ($profiler = $db->getProfiler ())) {
			$output = '';
			$totalTime    = $profiler->getTotalElapsedSecs();
			$queryCount   = $profiler->getTotalNumQueries();
			$longestTime  = 0;
			$longestQuery = null;
			foreach ($profiler->getQueryProfiles() as $query) {
				if ($query->getElapsedSecs() > $longestTime) {
					$longestTime  = $query->getElapsedSecs();
					$longestQuery = $query->getQuery();
				}
				$output .= 'Query : '. $query->getElapsedSecs(). ' for '. $query->getQuery(). "<br />\n";
			}
			$title .= ': executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds';			
			$output .= '<br />Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br />\n";
			$output .= 'Queries per second: ' . $queryCount / $totalTime . "<br />\n";
			$output .= 'Longest query length: ' . $longestTime . "<br />\n";
			$output .= "Longest query: <br />\n" . $longestQuery . "<br />\n";
			return self::display($output, $title, 'blue', $echo);
		}
	}
	
	/**
	 * returns a formatted traceback string
	 *
	 * @return string
	 */
	public static function getTraceback () {
		$callStack = debug_backtrace();
		$debugConsoleFiles = array(
			'Debug.php',
			'ErrorController.php'
		);
		
		$call = array (
			'file' => 'Debug.php'
		);
		
		while(isset ($call['file']) && in_array(basename($call['file']), $debugConsoleFiles)) {
			$call = array_shift($callStack);
		}
		if (!isset($call['file']))
			return;
		$fullTraceback = $call['file'] . ' on line ' . $call['line'];
		$call['file'] = self::cropScriptPath($call['file']);
		$traceback = '';
		if (JIMW_DEBUG_MODE) {
			$traceback = '<br /><acronym class="backtrace" title="' . $fullTraceback . '">';
		}
		$traceback .= $call['file'] . ' on line ';
		$traceback .= $call['line'];
		if (JIMW_DEBUG_MODE) {
			$traceback .= '</acronym>';
		}
		return $traceback;
	}
	
	/**
	 * crops long script path, shows only the last $maxLength chars
	 *
	 * @param string $path
	 * @param int $maxLength
	 * @return string
	 */
	protected static function cropScriptPath ($path, $maxLength = 30) {
		if (strlen($path) > $maxLength) {
			$startPos = strlen($path) - $maxLength - 2;
			
			if ($startPos > 0) {
				$path = '...' . substr($path, $startPos);
			}
		}

		return $path;
	}
}
