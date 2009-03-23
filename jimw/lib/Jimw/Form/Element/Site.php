<?php
class Jimw_Form_Element_Site extends Zend_Form_Element_Select
{
    protected function _getMultiOptions ()
    {
        if (!count($this->options)) {
            $sites = new Jimw_Site_Site();
            $select = $sites->select();
            $list = $sites->fetchAll($select);
            $array = array();
            foreach ($list as $site) {
                $array[$site->id] = $site->name;
            }
            $this->options = $array;
        }
        return $this->options;
    }
}