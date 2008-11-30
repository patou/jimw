<?php
/**
 * Jimw_Site_Dispatch
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Dispatch extends Zend_Controller_Dispatcher_Standard
{
    /**
     * Default Extention
     * @var string
     */
    protected $_defaultExt = 'phtml';
    /**
     * @return string
     */
    public function getDefaultExt ()
    {
        return $this->_defaultExt;
    }
/**
 * Formats a string into an action name.  This is used to take a raw
 * action name, such as one that would be stored inside a Zend_Controller_Request_Abstract
 * object, and reformat into a proper method name that would be found
 * inside a class extending Zend_Controller_Action.
 *
 * @param string $unformatted
 * @return string
 */
/*   public function formatActionName($unformatted)
    {
        $formatted = $this->_formatName($unformatted, true);
        return strtolower($formatted);
    }
*/
}
?>