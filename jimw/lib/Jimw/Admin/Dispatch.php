<?php
/**
 * Jimw_Admin_Dispatch
 *
 * @author	    Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Admin_Dispatch extends Zend_Controller_Dispatcher_Standard 
{
	/**
     * Default Extention
     * @var string
     */
    protected $_defaultExt = 'phtml';
	
	/**
	 * @return string
	 */
	public function getDefaultExt () {
		return $this->_defaultExt ;
	}
    
}

?>
