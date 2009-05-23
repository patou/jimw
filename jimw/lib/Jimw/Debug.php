<?php
/**
 *
 */
class Jimw_Debug extends Zend_Debug
{
    public static $output = '';
    public static $output_log = '';
    public static $active = false;
    
    public static function initDebug ($active = true)
    {
        $debug = new Jimw_Debug();
    	$errorhandler = array($debug , 'errorHandlerCallback');
        $displayEnd = array($debug , 'displayEnd');
        register_shutdown_function($displayEnd);
        set_error_handler($errorhandler);
        self::$active = $active;
    }
    /**
     * Display text at the end of the page
     *
     */
    public function displayEnd ()
    {
    	self::writeLog();
    	if (self::$active) {
            echo self::$output;
        }
    }
    
    public static function writeLog() {
    	$file = JIMW_REP_CACHE . 'logs/' ;
    	if (!file_exists($file))
    		mkdir($file);
    	$f = fopen($file . date("Y-m-d") . ".log", "a");
    	if ($f) {
	    	fwrite($f, date(DATE_ISO8601) . ":\n");
	    	fwrite($f, self::$output_log);
	    	fclose($f);
    	}
    }
    
    public static function log($msg, $title = '', $trace = true) {
    	if ($title != '')
    		self::$output_log .= strip_tags($title) . ":\n"; 
    	self::$output_log .= strip_tags($msg) . "\n";
    	if ($trace) {
	    	$callstack = debug_backtrace();
	    	foreach ($callstack as $call) {
	    		self::$output_log .= "\t" . $call['file'] . ' on line ' . $call['line'] . "\n";
	    	}
    	}
    }
    
    public static function disactive ()
    {
        self::$active = false;
    }
    /**
     * callback method for PHP errorhandling
     *
     * @TODO implement more errorlevels
     */
    public function errorHandlerCallback ()
    {
        $details = func_get_args();
        if (!strpos($details[2], 'Zend'.DIRECTORY_SEPARATOR.'Loader.php')) {
	        $details[1] = str_replace("'", '"', $details[1]);
	        $details[1] = str_replace('href="function.', 'target="_blank" href="http://www.php.net/', $details[1]);
	        $color = 'orange';
	        /* determine error level */
	        $errorlevel = 'error';
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
	        $infos .= 'PHP ' . strtoupper($errorlevel) . ' </strong>';
	        $infos .= $details[1] . "\n" . '<br /><acronym class="backtrace" title="' . $fullTraceback . '">';
	        $infos .= $file . ' on line ';
	        $infos .= $details[3] . '</span>';
	        self::display($infos, $details[1], $color, false, false);
        }
    }
    /**
     * Dump a var
     *
     * @param mixed $var The var to dump
     * @param string $label The title
     * @param boolean $echo
     * @return string|void
     */
    public static function dump ($var, $label = null, $echo = true)
    {
    	//self::log($label, print_r($var, true));
        return self::display(parent::dump($var, null, false), $label, 'yellow', $echo, false);
    }
    
    public static function display ($message, $title = '', $color = '#00E600', $echo = true, $log = true)
    {
        static $id = 0;
        $div_id = 'debug' . $id ++;
        if (! JIMW_DEBUG_MODE) {
            $output = "\n<!--\n";
        } else {
            if (empty($title)) {
                $pos = strpos($message, "\n");
                if ($pos === false || $pos < 1) {
                    $title = $message;
                    $message = '';
                } else {
                    if ($pos > 80)
                        $pos = 80;
                    $title = substr($message, 0, $pos) . ' ...';
                    $message = substr($message, strlen($title) - 4);
                }
            }
            $output = '<div style="border: 1px solid ' . $color . ';
        	border-left: 5px solid ' . $color . ';
        	background-color: white;
        	padding: 3px;
        	margin: 5px 3px;">' . "\n";
            $output .= '<a href="" onclick="el = document.getElementById(\'' . $div_id . '\'); el.style.display = (el.style.display == \'none\') ? \'block\' : \'none\'; return false;">';
            $output .= $title;
            $output .= '</a><div id="' . $div_id . '" style="display:none;text-align:left;">';
        }
        $output .= $message . "\n";
        $output .= self::getTraceback();
        if (! JIMW_DEBUG_MODE) {
            $output .= "\n-->\n";
        } else {
            $output .= '</div></div>' . "\n";
        }
        if ($echo) {
            self::$output .= $output;
        }
        if ($log)
        	self::log($message, $title, false);
        return $output;
    }
    /**
     * Display an exception
     *
     * @param Exception $e
     * @param unknown_type $echo
     * @return unknown
     */
    public static function display_exception (Exception $e, $echo = true)
    {
        $title = 'Exception [' . get_class($e) . '-' . $e->getCode() . '] : ' . $e->getMessage();
        $file = $e->getFile();
        $output = $title;
        $output .= ' in <acronym class="backtrace" title="' . $file . ' on line ' . $e->getLine() . '">' . self::cropScriptPath($e->getFile()) . ' on line ' . $e->getLine() . "\n<br />";
        $stack = $e->getTrace();
        //Zend_Debug::dump($stack);
        foreach ($stack as $item) {
            if ($item && isset($item['file']) && $item['line'])
                $output .= '<acronym class="backtrace" title="' . $item['file'] . ' on line ' . $item['line'] . ' ' . (isset($item['class']) ? $item['class'] . $item['type'] : '') . $item['function'] . '()">' . self::cropScriptPath($item['file']) . ' on line ' . $item['line'] . ' ' . (isset($item['class']) ? $item['class'] . $item['type'] : '') . $item['function'] . "()\n<br />";
        }
        self::log($e->__toString(), '', false);
        return self::display($output, $title, 'red', $echo, false);
    }
    
    public static function deprecated ($function, $instead = '', $echo = true)
    {
        $output = $function . ' is deprecated';
        if (! empty($instead)) {
            $output .= ' use ' . $instead . ' instead.';
        }
        $output .= "\n";
        try {
            throw new Exception();
        } catch (Exception $e) {
            $stack = $e->getTrace();
            array_shift($stack);
            //Zend_Debug::dump($stack);
            foreach ($stack as $item) {
                if ($item && isset($item['file']) && $item['line'])
                    $output .= '<acronym class="backtrace" title="' . $item['file'] . ' on line ' . $item['line'] . ' ' . (isset($item['class']) ? $item['class'] . $item['type'] : '') . $item['function'] . '()">' . self::cropScriptPath($item['file']) . ' on line ' . $item['line'] . ' ' . (isset($item['class']) ? $item['class'] . $item['type'] : '') . $item['function'] . "()\n<br />";
            }
        }
        return self::display($output, '', 'yellow', $echo, false);
    }
    
    public static function profile_db ($db, $title = '', $echo = true)
    {
        if ($db && ($profiler = $db->getProfiler())) {
            $output = '';
            $totalTime = $profiler->getTotalElapsedSecs();
            $queryCount = $profiler->getTotalNumQueries();
            $longestTime = 0;
            $longestQuery = null;
            $list = $profiler->getQueryProfiles();
            if (!empty($list))
            {
                foreach ($list as $query) {
                    if ($query->getElapsedSecs() > $longestTime) {
                        $longestTime = $query->getElapsedSecs();
                        $longestQuery = $query->getQuery();
                    }
                    $output .= 'Query : ' . $query->getElapsedSecs() . ' for ' . $query->getQuery() . "<br />\n";
                }
            }
            $title .= ': executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds';
            if ($queryCount != 0)
            {
                $output .= '<br />Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br />\n";
            }
            if ($totalTime != 0)
            {
                $output .= 'Queries per second: ' . $queryCount / $totalTime . "<br />\n";
            }
            $output .= 'Longest query length: ' . $longestTime . "<br />\n";
            $output .= "Longest query: <br />\n" . $longestQuery . "<br />\n";
            return self::display($output, $title, 'blue', $echo, false);
        }
    }
    /**
     * returns a formatted traceback string
     *
     * @return string
     */
    public static function getTraceback ()
    {
        $callStack = debug_backtrace();
        $debugConsoleFiles = array('Debug.php' , 'ErrorController.php');
        $call = array('file' => 'Debug.php');
        while (isset($call['file']) && in_array(basename($call['file']), $debugConsoleFiles)) {
            $call = array_shift($callStack);
        }
        if (! isset($call['file']))
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
    protected static function cropScriptPath ($path, $maxLength = 30)
    {
        if (strlen($path) > $maxLength) {
            $startPos = strlen($path) - $maxLength - 2;
            if ($startPos > 0) {
                $path = '...' . substr($path, $startPos);
            }
        }
        return $path;
    }
}
