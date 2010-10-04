<?php

/**
 * Blog_View_Helper_ListBlogMessage
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once(dirname(__FILE__) . '/../../Controller/Model/BlogMessage.php');
include_once(dirname(__FILE__) . '/../../Controller/Model/BlogComment.php');
include_once(dirname(__FILE__) . '/../../Controller/Model/BlogCommentForm.php');

class Blog_View_Helper_ListBlogMessage extends Zend_View_Helper_Abstract {

    /**
     * Display the menu
     *
     * @param string $name
     * @param boolean $display Auto render the menu with <li></li> tag
     * @return string|Jimw_Site_Tree_Row
     */
    public function listBlogMessage($tree_alias, $options = array('number' => 5)) {
        $messagetable = new BlogMessage();
        $treetable = new Jimw_Site_Tree();

        $tree = $treetable->findAlias($tree_alias)->current();
        if ($tree == null) {
            Jimw_Debug::display("Alias $tree_alias did't exist");
            return '';
        }

        $list = $messagetable->fetchAll($messagetable->select()->where('tree_parentid = ?', $tree->id)
                                ->where('blogmessage_date <= NOW()')
                                ->order('blogmessage_date DESC')
                                ->limit($options['number']));
        $html = '<ul>';
        //$url = $this->url(array('alias' => $this->tree->alias, 'ext' => 'phtml'), 'alias', true);
        foreach ($list as $message) {
            $tree = $treetable->find($message->tree_parentid)->current();
            $url = $this->view->url(array('module' => 'blog', 'controller' => 'message', 'action' => 'view', 'format' => 'phtml', 'alias' => $tree->alias, 'mid' => $message->id), 'format', true);
            $html .= '<li><a href="' . $url . '#' . $message->id . '">' . $message->title . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

}