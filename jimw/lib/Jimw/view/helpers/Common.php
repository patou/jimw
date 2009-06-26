<?php
/**
 *
 * @author desaintsteban.p
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * Common helper
 *
 * @uses viewHelper Jimw_View_Helper
 */
class Jimw_View_Helper_Common
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function common ($file)
    {
        if (!is_readable(JIMW_REP_PUBLIC_COMMON . $file)) {
            $modules = new Jimw_Site_Module();
            $modules_list = $modules->fetchAll();
            foreach ($modules_list as $module) {
                $path = JIMW_REP_MODULE . $module->path . '/views/common/' . $file;
                if (is_readable($path)) { //If exists
                    @copy($path, JIMW_REP_PUBLIC_COMMON . $file);
                    break;
                }
            }
        }
        return $this->view->baseUrl() . JIMW_URL_PUBLIC_COMMON_PATH . '/' .$file;
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
