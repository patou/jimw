<?php
/**
 *
 * @author patou
 * @version
 */
/**
 * formPerms helper
 *
 * @uses helper Jimw_View_Helper
 */
class Jimw_View_Helper_FormPerms extends Zend_View_Helper_FormRadio
{

    /**
     *
     */
    public function formPerms($name, $value = null, $attribs = null, $options = null, $listsep = null)
    {
        if ($value != Zend_Acl::TYPE_ALLOW && $value != Zend_Acl::TYPE_DENY)
            $value = '';
        return $this->formRadio($name, $value, $attribs, $options, $listsep);
    }
}
