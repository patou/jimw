<?php
include('jimw/lib/jimw_lang.class.php');
$lang = new jimw_lang('fr');
echo $lang['test'], ' - ', $lang->test_sprintf(' sqdfjkmlj ', 42);
var_dump($lang->_l);
?>