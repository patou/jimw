<?php
/**
 * Blog_View_Helper_MenuRSS
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: Menu.php 175 2008-03-10 18:05:19Z logisim $
 */
class Blog_View_Helper_MenuRSS extends Zend_View_Helper_Abstract
{
    /**
     * Get the menu list
     * If the menu item didn't exist get children the root tree (parentid = 0)
     *
     * @param string $name
     * @return array
     */
    private function getList ($name)
    {
        $modules = new Jimw_Site_Module();
        $trees = new Jimw_Site_Tree();
        $select = $trees->select()->where('tree_status = ?', Jimw_Site_Tree::PUBLISHED)->where("module_path = 'blog' OR module_path = 'categoryblog'")->order('tree_lft ASC');
        return $trees->fetchAll($select);
    }
    /**
     * Display the menu
     *
     * @param string $name
     * @param boolean $display Auto render the menu with <li></li> tag
     * @return string|Jimw_Site_Tree_Row
     */
    public function menuRSS ($name = '', $display = false)
    {
        $list = $this->getList($name);
        if ($display === false) {
            return $list;
        } else {
            return $this->display_menu($list, $display);
        }
    }
    private function display_menu ($menu, array $option)
    {
        $html = '<ul' . ((isset($option['ulClassName'])) ? ' class="' . $option['ulClassName'] . '">' : '>');
        foreach ($menu as $menuitem) {
            $liClass = '';
            if (isset($option['liCurrentClassName']) && $menuitem->getActive()) {
                $liClass = ' ' . $option['liCurrentClassName'];
            }
            $html .= '<li' . ((isset($option['ulClassName'])) ? ' class="' . $option['ulClassName'] . $liClass . '">' : '>');
            $aClass = (isset($option['aClassName'])) ? $option['aClassName'] : '';
            if (isset($option['currentClassName']) && $menuitem->getActive()) {
                $aClass .= ' ' . $option['currentClassName'];
            }
            $class = trim($aClass);
            if (! empty($class)) {
                $class = ' class="' . $class . '"';
            }
            $html .= '<a href="' . $this->view->url(array('alias' => $menuitem->alias , 'format' => 'rss'), 'alias', true) . '"' . $class . '>' . $menuitem->menutitle . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }
}