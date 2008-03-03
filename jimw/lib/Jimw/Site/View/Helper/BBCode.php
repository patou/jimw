<?php
/**
 * Jimw_Site_View_Helper_BBCode
 *
 * @author	   Logisim
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

class Jimw_Site_View_Helper_BBCode
{
	
public function bbCode($text) {
  $bbcode = array(
    "/\[b\](.+?)\[\/b\]/si",
    "/\[i\](.+?)\[\/i\]/si",
    "/\[u\](.+?)\[\/u\]/si",
    "/\[img\](.+?)\[\/img\]/si",
    "/\[img=(.+?)\](.+?)\[\/img\]/si",
    "/\[url\](.+?)\[\/url\]/si",
    "/\[url=(.+?)\](.+?)\[\/url\]/si",
    "/\[mail\](.+?)@(.+?)\[\/mail\]/si",
    "/\[quote\](.+?)\[\/quote\]/si",
    "/\[quote=(.+?)\](.+?)\[\/quote\]/si",
    "/\[color=([-#0-9a-zA-Z]+)\](.+?)\[\/color\]/si",
    "/\[mp3\](.+?)\[\/mp3\]/si",
    "/\[mp3=(.+?)\](.+?)\[\/mp3\]/si",
    "/\[flv\](.+?),(.+?),([0-9]+),([0-9]+)\[\/flv\]/si",
    "/\[flv\](.+?),(.+?)\[\/flv\]/si",
    "/\[flvchange\](.+?),(.+?),(.+?)\[\/flvchange\]/si"
  );
  $htmlcode = array(
    "<b>$1</b>",
    "<i>$1</i>",
    "<u>$1</u>",
    "<img src=\"$1\" border=\"0\" alt=\"\" />",
    "<img src=\"$2\" border=\"0\" alt=\"$1\" title=\"$1\" />",
    "<a href=\"$1\" target=\"_blank\">$1</a>",
    "<a href=\"$1\" target=\"_blank\">$2</a>",
    "<a href=\"mailto:$1&#64;$2\">$1&#64;$2</a>",
    "<span class=\"quote\">$1</span>",
    "<span class=\"quote\">$2</span>",
    "<span style=\"color: $1;\">$2</span>",
    "<object type=\"application/x-shockwave-flash\" data=\"".$this->path."/dewplayer.swf?son=".$this->path."/$1\" height=\"20\" width=\"200\"><param name=\"movie\" value=\"".$this->path."/dewplayer.swf?son=".$this->path."/$1\"></object>",
    "<object type=\"application/x-shockwave-flash\" data=\"".$this->path."/dewplayer.swf?son=".$this->path."/$1\" height=\"20\" width=\"200\"><param name=\"movie\" value=\"".$this->path."/dewplayer.swf?son=".$this->path."/$1\"></object><p align=\"center\"><small>$1</small></p>",
    "<p id=\"player$1\">Vous devez t&eacute;l&eacute;charger <a href=\"http://www.macromedia.com/go/getflashplayer\">Flash Player</a> pour voir cette vid&eacute;o.</p>
<script type=\"text/javascript\">
	var s1 = new SWFObject(\"".$this->path."/flvplayer.swf\",\"single\",\"$3\",\"$4\",\"7\");
	s1.addParam(\"allowfullscreen\",\"true\");
	s1.addVariable(\"file\",\"/public/rn2007/videos/$2.flv\");
	s1.addVariable(\"image\",\"/public/rn2007/videos/$2.jpg\");
	s1.addVariable(\"width\",\"$3\");
	s1.addVariable(\"height\",\"$4\");
	s1.write(\"player$1\");
</script>",
    "<p id=\"player$1\">Vous devez t&eacute;l&eacute;charger <a href=\"http://www.macromedia.com/go/getflashplayer\">Flash Player</a> pour voir cette vid&eacute;o.</p>
<script type=\"text/javascript\">
	var s1 = new SWFObject(\"".$this->path."/flvplayer.swf\",\"single\",\"400\",\"300\",\"7\");
	s1.addParam(\"allowfullscreen\",\"true\");
	s1.addVariable(\"file\",\"/public/rn2007/videos/$2.flv\");
	s1.addVariable(\"image\",\"/public/rn2007/videos/$2.jpg\");
	s1.addVariable(\"width\",\"400\");
	s1.addVariable(\"height\",\"300\");
	s1.write(\"player$1\");
</script>",
    "<a href=\"#\" onclick=\"javascript:s1.innerHTML=''; s1.addVariable('file','/public/rn2007/videos/$2.flv'); s1.addVariable('image','/public/rn2007/videos/$2.jpg'); s1.write('player$1'); return false;\">$3</a>"
  );
	}
	return preg_replace($bbcode,$htmlcode,typofr($text));
}