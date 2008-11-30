<?php
function typofr($text) {
  $source = array(
    "/&laquo; /si",
    "/ &raquo;/si",
    "/« /si",
    "/ »/si",
    "/ :/si",
    "/ !/si",
    "/ \?/si",
    "/ ;/si"
  );
  //,    "/([0-9]) ([a-zA-Z])/si"
  $dest = array(
    "&laquo;&nbsp;",
    "&nbsp;&raquo;",
    "&laquo;&nbsp;",
    "&nbsp;&raquo;",
    "&nbsp;:",
    "&nbsp;!",
    "&nbsp;?",
    "&nbsp;;"
  );
  //,    "$1&nbsp;$2"

  return preg_replace($source,$dest,$text);
}
?>
