<?php
class Jimw_Form_Element_Domain extends Zend_Form_Element_Select
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    protected function _getMultiOptions ()
    {
        if (!count($this->options)) {
            $domains = new Jimw_Site_Domain();
            $database = Zend_Registry::get('database');
            $site = Zend_Registry::get('site');
            $list = $domains->fetchAll($domains->select()->where('database_id = ?', $database->id)->where('site_id = ?', $site->id));
            $array = array();
            foreach ($list as $domain) {
                $array[$domain->id] = $domain->__toString();
            }
            $this->options = $array;
        }
        return $this->options;
    }
}