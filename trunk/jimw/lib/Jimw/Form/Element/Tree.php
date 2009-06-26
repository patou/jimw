<?php
class Jimw_Form_Element_Tree extends Zend_Form_Element_Select
{
    private $_allowRoot = true;
    private $_siteId = null;
    private $_filterModule = null;
    /**
     * Retrieve options array
     *
     * @return array
     */
    protected function _getMultiOptions ()
    {
        if (! count($this->options)) {
            $tree = new Jimw_Site_Tree();
            $site = Zend_Registry::get('site');
            //$list_tree = $tree->find($site->tree_id)->current();
            $list_tree = $tree->getChildren($site->tree_id);
            if ($this->_allowRoot) {
                $options = array(0 => $this->_translateValue('root'));
            } else {
                $options = array();
            }
            $options += $this->_build($list_tree);
            $this->options = $options;
        }
        return $this->options;
    }
    /**
     * Build the tree list
     * @param $parent
     * @return unknown_type
     */
    private function _build (Jimw_Site_Tree_Rowset $parent)
    {
        $list = array();
        $iterator = new RecursiveIteratorIterator($parent, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $tree) {
            $indent = '';
            if ($this->getSiteId() != null && $tree->site_id != $this->getSiteId())
                continue;
            if ($this->_filterModule != null && $tree->module_path != $this->_filterModule)
                continue;
            for ($i = 0; $i < $iterator->getDepth(); $i ++)
                $indent .= '--';
            $list[(string) $tree->id] = $indent . $tree->menutitle;
        }
        return $list;
    }
    /**
     * @return boolean
     */
    public function getAllowRoot ()
    {
        return $this->_allowRoot;
    }
    /**
     * Allow select the root tree
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
     * Filter the list with just page from the given id
     * @param int $siteId
     */
    public function setSiteId ($siteId)
    {
        $this->_siteId = $siteId;
        return $this;
    }
    /**
     * Filter the list with just the module given
     * @param string $filterModule the $filterModule to set
     */
    public function setFilterModule ($filterModule)
    {
        $this->_filterModule = $filterModule;
    }
    /**
     * @return the $filterModule
     */
    public function getFilterModule ()
    {
        return $this->_filterModule;
    }
}