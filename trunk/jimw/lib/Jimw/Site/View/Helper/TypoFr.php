<?php
/**
 * Jimw_Site_View_Helper_TypoFr
 *
 * @author	   Logisim
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_View_Helper_TypoFr
{
    function typoFr ($text)
    {
        $source = array("/&laquo; /si" , "/ &raquo;/si" , "/� /si" , "/ �/si" , "/ :/si" , "/ !/si" , "/ \?/si" , "/ ;/si");
        //,    "/([0-9]) ([a-zA-Z])/si"
        $dest = array("&laquo;&nbsp;" , "&nbsp;&raquo;" , "&laquo;&nbsp;" , "&nbsp;&raquo;" , "&nbsp;:" , "&nbsp;!" , "&nbsp;?" , "&nbsp;;");
        //,    "$1&nbsp;$2"
        return preg_replace($source, $dest, $text);
    }
}