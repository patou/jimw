<?php
/**
 * Jimw_Site_View_Helper_ProtectMail
 *
 * @author	   Logisim
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_View_Helper_ProtectMail extends Zend_View_Helper_Abstract
{
    public function protectMail ($text)
    {
        $source = array("/(\W[\w\-]+[\w\.\-]*)@([\w\-\.]+\.[a-zA-Z]{2,5}\W)/si");
        $dest = array("$1<span style=\"font-weight: bold; font-style: italic; color: rgb(153, 153, 153);\">[arobase]</span>$2");
        return preg_replace($source, $dest, $text);
    }
}