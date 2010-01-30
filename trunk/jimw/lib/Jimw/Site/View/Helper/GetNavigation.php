<?php
/**
 *
 * @author desaintsteban.p
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * GetNavigation helper
 *
 * @uses viewHelper Jimw_Site_View_Helper
 */
class Jimw_Site_View_Helper_GetNavigation
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Get a Zend_Navigation from a parent tree id or an alias
     * @return Zend_Navigation
     */
    public function getNavigation ($tree)
    {
        $trees = new Jimw_Site_Tree();
        if (is_string($tree)) {
            $tree_root = $trees->findAlias($tree)->getChildren();
        }
        else if (is_integer($tree)) {
            $tree_root = $trees->getChildren($tree);
        }
        else {
            $tree_root = $trees->getChildren(0);
        }
        if ($tree_root->count() > 0) {
            $navigation = new Zend_Navigation();
            foreach ($tree_root as $page) {
                $p = new Jimw_Site_Tree_Navigation(array('tree' => $page));
                $navigation->addPage($p);
            }
            return $navigation;
        }
        return null;
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
