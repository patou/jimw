<?php
/**
 * Jimw_Site_View_Helper_Navig
 *
 * @author	   Logisim
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2009 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: $
 */
class Jimw_Site_View_Helper_Navig extends Zend_View_Helper_Abstract
{
    /**
     * Get the navig list
     *
     * @param Jimw_Site_Tree_Row $menu
     * @return Jimw_Site_Tree_Row
     */
    public function getNavig ($menu) {
        foreach ($menu as $menuitem) {
            if ($menuitem->getExpanded() || $menuitem->getActive()) {
                if ($menuitem->hasChildren()) {
                    return array_merge(array($menuitem), $this->getNavig($menuitem->getChildren()));
                } else {
								    return array($menuitem);
								}
            }
        }
        return array();
    }
    /**
     * Display the navigation
     *
     * @param string $name
     * @param boolean $display Auto render the navig with <li></li> tag
     * @return array|Jimw_Site_Tree_Row
     */
    public function navig ($name = '', $display = false)
    {
        $menu = $this->view->menu($name, false);
        $navig = $this->getNavig($menu);
        if ($display === false) {
            return $navig;
        } else {
            return $this->display_navig($navig, $display);
        }
        return array();
    }
    private function display_navig ($navig, array $option)
    {
        return print_r($navig, true);
				/*$html = '<ul' . ((isset($option['ulClassName'])) ? ' class="' . $option['ulClassName'] . '">' : '>');
        foreach ($menu as $menuitem) {
            if ($menuitem->isVisible()) {
                $liClass = '';
                if (isset($option['liCurrentClassName']) && $menuitem->getActive()) {
                    $liClass = ' ' . $option['liCurrentClassName'];
                }
                $liId = '';
                if (isset($option['liCurrentIdName']) && $menuitem->getActive()) {
                    $liId = ' id="' . $option['liCurrentIdName'] . '"';
                }
                $html .= '<li' . ((isset($option['ulClassName'])) ? ' class="' . $option['ulClassName'] . $liClass . '"' : '') . $liId . '>';
                $aClass = (isset($option['aClassName'])) ? $option['aClassName'] : '';
                if (isset($option['currentClassName']) && $menuitem->getActive()) {
                    $aClass .= ' ' . $option['currentClassName'];
                }
                $class = trim($aClass);
                if (! empty($class)) {
                    $class = ' class="' . $class . '"';
                }
                $html .= '<a href="' . $this->view->url(array('alias' => $menuitem->alias , 'format' => 'phtml'), 'alias', true) . '"' . $class . '>';
                if (isset($option['addSpan']) && $option['addSpan'])
                    $html .= '<span>' . $menuitem->menutitle . '</span>';
                else
                    $html .= $menuitem->menutitle;
                $html .= '</a></li>';
                if (isset($option['menuDepth']) && $option['menuDepth'] > 1) {
                    if ($menuitem->hasChildren() && ((isset($option['menuExpanded']) && ! $option['menuExpanded']) || $menuitem->getExpanded())) {
                        $html .= $this->display_menu($menuitem->getChildren(), array_merge($option, array('menuDepth' => $option['menuDepth'] - 1)));
                    }
                }
            }
        }
        $html .= '</ul>';
        return $html;*/
    }
}