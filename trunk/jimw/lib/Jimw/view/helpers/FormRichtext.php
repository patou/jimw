<?php
/**
 *
 * @author patou
 * @version
 */
/**
 * formRichtext helper
 *
 * @uses helper Jimw_View_Helper
 */
class Jimw_View_Helper_FormRichtext extends Zend_View_Helper_FormTextarea
{

    /**
     *
     */
    public function formRichtext($name, $value = null, $attribs = null)
    {
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/javascripts/jimw.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/fckeditor.js');
        $this->view->headScript()->captureStart();
        ?>
      addOnloadScript(function()
      {
        var oFCKeditor = new FCKeditor('<?php echo $name ?>') ;
        oFCKeditor.BasePath = "<?php echo $this->view->path ?>/" ;
        oFCKeditor.Config["LinkBrowserURL"] = '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>';
        oFCKeditor.Config["ImageBrowserURL"] = '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>';
        oFCKeditor.Config["FlashBrowserURL"] = '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>';
        oFCKeditor.Width = 600;
		oFCKeditor.Height = 400;
        oFCKeditor.ReplaceTextarea();
      });
        <?php
        $this->view->headScript()->captureEnd();
        $button = '<input type="button" value="';
        $button .= $this->view->_('Add picture');
        $button .= '"/>';
        $button .= '<input type="button" value="';
        $button .= $this->view->_('Add links');
        $button .= '"/>';
        $button .= '<input type="button" value="';
        $button .= $this->view->_('Add files');
        $button .= '"/>';
        return $button . $this->formTextarea($name, $value, $attribs);
    }
}
