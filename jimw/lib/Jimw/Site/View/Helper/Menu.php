<?php
/**
 * Jimw_Site_View_Helper_Menu
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Jimw_Site_View_Helper_Menu
{
	public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
	
	/**
	 * Get the menu list
	 * If the menu item didn't exist get children the root tree (parentid = 0)
	 *
	 * @param string $name
	 * @return array
	 */
	private function getMenu ($name)
	{
		$menu = new Jimw_Site_Menu();
		$tree = new Jimw_Site_Tree();
		$result = $menu->getMenu($name);
		if (!count($result)) {
			return $tree->getChildren(0);
		} else {
			$list = null;
			foreach ($result as $item) {
				if ($item->status == 0) {
					if ($list === null) {
						$list = $tree->getChildren($item->tree_id);
					} else {
						$list->addRows($tree->getChildren($item->tree_id));
					}
				} else {
					if ($list === null) {
						$list = $tree->find($item->tree_id);
					} else {
						$list->addRows($tree->find($item->tree_id));
					}
				}
			}
			return ($list === null) ? array() : $list;
		}
	}

	/**
	 * Display the menu
	 *
	 * @param string $name
	 * @param boolean $display Auto render the menu with <li></li> tag
	 * @return string|Jimw_Site_Tree_Row
	 */
	public function menu ($name = '', $display = false)
	{
		$menu = $this->getMenu ($name);
		if ($display === false) {
			return $menu;
		} else {
			return $this->display_menu ($menu, $display);
		}
	}

	private function display_menu ($menu, array $option)
	{
		$html = '<ul' . ((isset($option['ulClassName']))? ' class="' . $option['ulClassName'] . '">' : '>');
		foreach ($menu as $menuitem) {
			$liClass = '';
			if (isset($option['liCurrentClassName']) && $menuitem->getActive ()) {
				$liClass = ' ' . $option['liCurrentClassName'];
			}
			$html .= '<li' . ((isset($option['ulClassName']))? ' class="' . $option['ulClassName'] . $liClass . '">' : '>');
			$aClass = (isset($option['aClassName'])) ? $option['aClassName'] : '';
			if (isset($option['currentClassName']) && $menuitem->getActive ()) {
				$aClass .= ' ' . $option['currentClassName'];
			}
			$class = trim($aClass);
			if (!empty($class)) {
				$class = ' class="' . $class . '"';
			}
			if (isset($option['addSpan']) && $option['addSpan']) $html .= '<span>';
			$html .= '<a href="' . $this->view->url (array('alias' => $menuitem->alias, 'ext' => 'phtml'), 'alias', true) . '"'  . $class . '>' . $menuitem->menutitle . '</a>';
			if (isset($option['addSpan']) && $option['addSpan']) $html .= '</span>';
			$html .= '</li>';
			if (isset($option['menuDepth']) && $option['menuDepth'] > 1) {
				if ($menuitem->hasChildren () && ( (isset($option['menuExpanded']) && !$option['menuExpanded']) || $menuitem->getExpanded () )) {
					$html .= $this->display_menu($menuitem->getChildren(), array_merge($option, array('menuDepth' => $option['menuDepth'] - 1)));
				}
			}
		}
		$html .= '</ul>';
		return $html;
	}
}