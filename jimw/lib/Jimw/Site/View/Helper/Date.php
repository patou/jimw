<?php
/**
 *
 * @author patou
 * @version
 */
/**
 * Date helper
 *
 * @uses helper Zend_View_Helper
 */
class Jimw_Site_View_Helper_Date extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_View_Interface
     */
    public $view;
    /**
     *
     */
    public function date ($date, $output_format = null, $input_format = null)
    {
        if ($input_format === NULL)
            $input_format = Zend_Date::ISO_8601;
        $d = new Zend_Date($date, $input_format);
        return $d->toString($output_format);
    }
    /**
     * Sets the view field
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
