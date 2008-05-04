<?php
/**
 * Jimw_Form
 * Extend the Zend_Form
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Form.php 157 2008-02-07 13:19:58Z patou.de.saint.steban $
 */


class Jimw_Form extends Zend_Form {
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setMethod('POST');
        $this->addPrefixPath('Jimw_Form', JIMW_REP_LIB . 'Jimw/Form');   
    }
}

?>
