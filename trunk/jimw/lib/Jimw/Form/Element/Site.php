<?php
class Jimw_Form_Element_Site extends Zend_Form_Element_Select
{
    protected function _getMultiOptions ()
    {
        if (!count($this->options)) {
            $domains = new Jimw_Site_Site();
            $select = $domains->select();
            $list = $domains->fetchAll($select);
            $array = array();
            foreach ($list as $domain) {
                $array[$domain->id] = $domain->name;
            }
            $this->options = $array;
        }
        return $this->options;
    }
}