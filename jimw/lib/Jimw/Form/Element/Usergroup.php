<?php
class Jimw_Form_Element_Usergroup extends Zend_Form_Element_Multiselect
{
    protected $_usergrouplist = null;
    protected function _getMultiOptions ()
    {
        if (!count($this->options)) {
            $users = new Jimw_Site_Group();
            $select = $users->select();
            $list = $users->fetchAll($select->where('group_id >= 3'));
            $array = array();
            foreach ($list as $user) {
                $array[$user->id] = $user->name;
            }
            $this->_usergrouplist = $array;
            $this->options = $array;
        }
        return $this->options;
    }

    public function getUsergroupList() {
        if ($this->_usergrouplist == NULL)
            $this->_getMultiOptions();
        return $this->_usergrouplist;
    }
}