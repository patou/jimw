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

class Jimw_Site_View_Helper_AliasLink
{
	
	public function aliasLink($text) {
 
        $tree = new Jimw_Site_Tree();
        $urlHelper = new Jimw_Site_View_Helper_Url();
        $result = $text;
        
        // Without text
        $count = preg_match_all('/\[\[([^\[\]\s\|]+)\]\]/', $text, $matches);
        for ($i=0; $i<$count; $i++) {
			$page = $tree->fetchRow(array('tree_alias = ?' => $matches[1][$i]));
        	if ($page) {
        		$url = $urlHelper->url(array('alias' => $matches[1][$i]), 'alias', true);
        		$result = str_replace($matches[0][$i], '<a href="'.$url.'">'.$page->pagetitle.'</a>', $result);
        	} else {
				$result = str_replace($matches[0][$i], '<a href="#">???</a>', $result);
			}
        }

        // With text
        $count = preg_match_all('/\[\[([^\[\]\s\|]+)[\s\|]([^\[\]]+)\]\]/', $text, $matches);
        for ($i=0; $i<$count; $i++) {
        	//$url = $urlHelper->url(array('alias' => $matches[1][$i]), 'alias', true);
			$page = $tree->fetchRow(array('tree_alias = ?' => $matches[1][$i]));
        	if ($page) {
        		$url = $urlHelper->url(array('alias' => $matches[1][$i]), 'alias', true);
        		$result = str_replace($matches[0][$i], '<a href="'.$url.'">'.$matches[2][$i].'</a>', $result);
        	} else {
				$result = str_replace($matches[0][$i], '<a href="#">???</a>', $result);
			}
        }

        return $result;
	}
	
}