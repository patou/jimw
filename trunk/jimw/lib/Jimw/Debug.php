<?php

/**
 *
 */
class Jimw_Debug extends Zend_Debug {
	public static function initDebug ()
	{
		$errorhandler = array (
			new Jimw_Debug (),
			'errorHandlerCallback'
		);
		
		set_error_handler($errorhandler);
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
		
		self::display($infos, $color);
	}
	
	public static function dump($var, $label=null, $echo=true)
	{
		return self::display(parent::dump($var, null, false), $echo);
	}
	
	public static function display ($message, $color = '#00E600',$echo = true)
	{
		$output = '<div style="border: 1px solid ' .$color . ';
        	border-left: 5px solid ' .$color . ';
        	background-color: white;
        	padding: 3px;
        	margin: 5px 3px;">' . "\n";
		$output .= $message . "\n";
		$output .= self::getTraceback();
		$output .= '</div>' . "\n";
		if (!JIMW_DEBUG_MODE)
			$output = "\n<!--\n" . $output . "\n-->\n";
		if ($echo) {
            echo($output);
        }
        return $output;
	}
	
	public static function display_exception (Exception $e, $echo = true)
	{
		$output = 'Exception ['. get_class($e). '-'. $e->getCode(). '] : ';
		$output .= $e->getMessage();
        return self::display($output, 'red', $echo);
	}
	
	public static function profile_db($db)
	{
		/*if ($db) {
			$output = '';
			/*$profiler = $db->getProfiler ();
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
			
				$output .= 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "<br />\n";
				$output .= 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br />\n";
				$output .= 'Queries per second: ' . $queryCount / $totalTime . "<br />\n";
				$output .= 'Longest query length: ' . $longestTime . "<br />\n";
				$output .= "Longest query: <br />\n" . $longestQuery . "<br />\n";
			return self::display($output, 'blue', $echo);
		}*/
	}
	
	/**
	 * returns a formatted traceback string
	 *
	 * @return string
	 */
	public static function getTraceback () {
		$callStack = debug_backtrace();
		$debugConsoleFiles = array(
			'Debug.php'
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
		
		$traceback = '<acronym class="backtrace" title="' . $fullTraceback . '">';
		$traceback .= $call['file'] . ' on line ';
		$traceback .= $call['line'] . '</acronym>';
		
		return '<br />' . $traceback;
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

?>
