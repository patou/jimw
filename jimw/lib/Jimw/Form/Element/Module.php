<?php
class Jimw_Form_Element_Module extends Zend_Form_Element_Select
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    protected function _getMultiOptions ()
    {
        if (!count($this->options)) {
            $modules = new Jimw_Site_Module();
            $list = $modules->fetchAll($modules->select());
            $array = array();
            foreach ($list as $module) {
                $array[$module->path] = $module->name;
            }
            $this->options = $array;
        }
        return $this->options;
    }
}