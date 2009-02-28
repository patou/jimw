<?php

class Jimw_Install_View_Helper_OkKo extends Zend_View_Helper_Abstract
{
    public function okKo($result){
        return $result ? '<font color=green>OK</font>' : '<font color=red>KO</font>';
    }
}
?>