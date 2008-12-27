<?php
/**
 * Jimw_Site_View_Helper_AliasLink
 *
 * @author	   Logisim
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Jimw_Site_View_Helper_AliasLink extends Zend_View_Helper_Abstract
{
  public function aliasLink($text) {

    $tree = new Jimw_Site_Tree();
    $result = $text;
    $matches = array();

    // Without text
    $count = preg_match_all('/\[\[([^\[\]\s\|]+)\]\]/', $text, $matches);
    for ($i=0; $i<$count; $i++) {
      $page = $tree->fetchRow(array('tree_alias = ?' => $matches[1][$i]));
      if ($page) {
        $url = $this->view->url(array('alias' => $matches[1][$i]), 'alias', true);
        $result = str_replace($matches[0][$i], '<a href="'.$url.'">'.$page->pagetitle.'</a>', $result);
      } else {
        $result = str_replace($matches[0][$i], '<a href="#">???</a>', $result);
			}
    }

    // With text
    $count = preg_match_all('/\[\[([^\[\]\s\|\{\}#]+)(#[^\[\]\s\|\{\}#]+)?(\{[^\[\]\{\}\s\|]+\})?[\s\|]([^\[\]]+)\]\]/', $text, $matches);
    for ($i=0; $i<$count; $i++) {
      //$url = $urlHelper->url(array('alias' => $matches[1][$i]), 'alias', true);
      $page = $tree->fetchRow(array('tree_alias = ?' => $matches[1][$i]));
      if ($page) {
        $url = $this->view->url(array('alias' => $matches[1][$i]), 'alias', true);
        $param = ($matches[3][$i] == '' ? '' : str_replace('&quot;','"',substr($matches[3][$i],1,-1).' '));
        $result = str_replace($matches[0][$i], '<a '.$param.'href="'.$url.$matches[2][$i].'">'.$matches[4][$i].'</a>', $result);
      } else {
        $result = str_replace($matches[0][$i], '<a href="#">???</a>', $result);
      }
    }

    return $result;
	}
	
}