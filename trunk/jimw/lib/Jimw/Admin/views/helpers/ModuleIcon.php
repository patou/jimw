<?php
class Jimw_Admin_View_Helper_ModuleIcon extends Zend_View_Helper_Abstract
{
    public function moduleIcon ($module)
    {
        $found = true;
        if (! is_readable(JIMW_REP_ADMIN_MODULE_ICON . $module . '.png')) {
            if (is_readable(JIMW_REP_MODULE . $module . '/favicon.png')) {
                @copy(JIMW_REP_MODULE . $module . '/favicon.png', JIMW_REP_ADMIN_MODULE_ICON . $module . '.png');
            } else {
                $found = false;
            }
        }
        echo '<img src="', $this->view->baseUrl(), JIMW_URL_ADMIN_MODULE_ICON, (($found) ? $module : 'article'), '.png" width="16" height="16" alt="', $module, '" title="', $module, '" border="0"/>';
    }
}
?>
