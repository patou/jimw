<?php


class Jimw_Global_Layout extends Zend_Layout {
    protected $_helperClass = 'Jimw_Global_Layout_Helper_Layout';
    protected $_pluginClass = 'Jimw_Global_Layout_Plugin';
    
    /**
     * Static method for initialization with MVC support
     * 
     * @param  string|array|Zend_Config $options 
     * @return Zend_Layout
     */
    public static function startMvc($options = null)
    {
        if (null === self::$_mvcInstance) {
            self::$_mvcInstance = new self($options, true);
        } else {
            self::$_mvcInstance->setOptions($options);
        }

        return self::$_mvcInstance;
    }
    
}

?>
