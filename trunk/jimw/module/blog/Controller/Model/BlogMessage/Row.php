<?php
/**
 * BlogMessage_Row
 *
 * @author	   Logisim
 * @category   Jimw_Blog
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class BlogMessage_Row extends Jimw_Db_Row
{
    public function getFirstComments($number = 10) {
        $commentstable = new BlogComment();
        $paginator = Zend_Paginator::factory($commentstable->select()->where('blogmessage_id = ?', $this->id));
        $paginator->setItemCountPerPage($number);
        return $paginator;
    }
}
?>
?>