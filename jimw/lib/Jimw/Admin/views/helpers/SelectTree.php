<?php
class Jimw_Admin_View_Helper_SelectTree extends Zend_View_Helper_FormElement
{
    public function selectTree ($name, $value = null, $attribs = null)
    {
        Jimw_Debug::deprecated("helper::selectTree()", "form::Tree()");
        $tree = new Jimw_Site_Tree();
        $session = new Zend_Session_Namespace('Admin');
        if (isset($session->site) && isset($session->site->tree_id))
            $parentid = $tree->find($session->site->tree_id)->current();
        else
            $parentid = null;
        $list_tree = $tree->getChildren($parentid ? $parentid->parentid : 0);
        $options = array(0 => $this->view->_('root')) + $this->_build($list_tree);
        return $this->view->formSelect($name, $value, $attribs, $options);
    }
    private function _build ($parent, $prof = 1)
    {
        $list = array();
        foreach ($parent as $tree) {
            $indent = '';
            for ($i = 0; $i < $prof; $i ++)
                $indent .= '--';
            $list[(string) $tree->id] = $indent . $tree->menutitle;
            if ($tree->hasChildren()) {
                $ret = $this->_build($tree->getChildren(), $prof + 1);
                $list = $list + $ret;
            }
        }
        return $list;
    }
}
?>
