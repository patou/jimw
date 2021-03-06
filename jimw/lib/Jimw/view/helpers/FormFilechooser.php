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
class Jimw_View_Helper_FormFilechooser extends Zend_View_Helper_FormText
{

    /**
     *
     */
    public function formFilechooser($name, $value = null, $attribs = null)
    {
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/javascripts/jimw.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/fckeditor.js');
        $this->view->headScript()->captureStart();
        $id = str_replace('-', '_',$this->_normalizeId($name));
        ?>
      setImagechooser<?php echo $id ?> = function(url) {
		if (url.length > 0 && document.getElementById) {
			document.getElementById('<?php echo $this->_normalizeId($name); ?>').value = url;
		}
      }
      openImagechooser<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setImagechooser'.$id), 'format', true) ?>", 800, 600);
      }
        <?php
        $this->view->headScript()->captureEnd();
        $button = '<input type="button" class="ImagechooserButton" value="';
        $button .= $this->view->_('Select picture');
        $button .= '" onclick="openImagechooser' . $id;
        $button .= '()"/>';

        return $this->formText($name, $value, $attribs) . $button;
    }
}
