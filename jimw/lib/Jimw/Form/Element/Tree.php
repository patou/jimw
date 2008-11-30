<?php
class Jimw_Form_Element_Tree extends Zend_Form_Element_Select
{
    private $_allowRoot = true;
    private $_siteId = null;
    /**
     * Retrieve options array
     *
     * @return array
     */
    protected function _getMultiOptions ()
    {
        if (!count($this->options)) {
            $tree = new Jimw_Site_Tree();
            $site = Zend_Registry::get('site');
            $list_tree = $tree->getChildren($site->tree_id);
            if ($this->_allowRoot)
                $options = array(
                0 => $this->_translateValue('root'));
            else
                $options = array();
            $options += $this->_build($list_tree);
            $this->options = $options;
        }
        return $this->options;
    }

    private function _build ($parent, $prof = 1)
    {
        $list = array();
        foreach ($parent as $tree) {
            $indent = '';
            if ($this->getSiteId() != null && $tree->site_id != $this->getSiteId())
                continue;
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
    /**
     * @return unknown
     */
    public function getAllowRoot ()
    {
        return $this->_allowRoot;
    }
    /**
     * @param boolean $allowRoot
     */
    public function setAllowRoot ($allowRoot)
    {
        $this->_allowRoot = $allowRoot;
    }

    /**
     * @return int
     */
    public function getSiteId ()
    {
        return $this->_siteId;
    }
    /**
     * @param int $siteId
     */
    public function setSiteId ($siteId)
    {
        $this->_siteId = $siteId;
        return $this;
    }
}