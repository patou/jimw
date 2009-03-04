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
class Jimw_View_Helper_FormDirchooser extends Zend_View_Helper_FormText
{

    /**
     *
     */
    public function formDirchooser($name, $value = null, $attribs = null)
    {
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/javascripts/jimw.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/fckeditor.js');
        $this->view->headScript()->captureStart();
        $id = str_replace('-', '_',$this->_normalizeId($name));
        ?>
      setDirchooser<?php echo $id ?> = function(node) {
		if (node && document.getElementById) {
			document.getElementById('<?php echo $this->_normalizeId($name); ?>').value = node.id;
		}
      }
      openDirchooser<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'file', 'action' => 'dirchooser', 'format' => 'ajax', 'javascript_fct' => 'setDirchooser'.$id), 'format', true) ?>", 200, 300);
      }
        <?php
        $this->view->headScript()->captureEnd();
        $button = '<input type="button" class="DirchooserButton" value="';
        $button .= $this->view->_('Select directory');
        $button .= '" onclick="openDirchooser' . $id;
        $button .= '()"/>';

        return $this->formText($name, $value, $attribs) . $button;
    }
}
