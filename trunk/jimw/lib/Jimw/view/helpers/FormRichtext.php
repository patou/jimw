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
        $options = array('width' => 600, 'height' => 400, 'toolbarSet' => 'Default');
        if (isset($attribs['editor'])) {
            $options = array_merge($options ,$attribs['editor']);
            unset($attribs['editor']);
        }
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/javascripts/jimw.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/fckeditor.js');
        $this->view->headScript()->captureStart();
        $id = str_replace('-', '_',$this->_normalizeId($name));
        ?>
      addOnloadScript(function()
      {
        var oFCKeditor = new FCKeditor('<?php echo $name ?>', <?php echo $options['width'] ?>, <?php echo $options['height'] ?>, '<?php echo $options['toolbarSet'] ?>') ;
        oFCKeditor.BasePath = "<?php echo $this->view->path ?>/" ;
        oFCKeditor.Config["LinkBrowserURL"] = '<?php echo $this->view->url(array('controller' => 'tree', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>';
        oFCKeditor.Config["ImageBrowserURL"] = '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>';
        oFCKeditor.Config["FlashBrowserURL"] = '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>';
        oFCKeditor.ReplaceTextarea();
      });
      setimagerichtext<?php echo $id ?> = function(url) {
      	if (url.length > 0) {
			var oEditor = FCKeditorAPI.GetInstance('<?php echo $name ?>');
			oEditor.InsertHtml( '<img src="'+url+'" />' );
		}
      }
      openimagerichtext<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setimagerichtext'.$id), 'format', true) ?>", 800, 600);
      }
      setlinkrichtext<?php echo $id ?> = function(node) {
      	if (node) {
			var oEditor = FCKeditorAPI.GetInstance('<?php echo $name ?>');
			oEditor.InsertHtml( '<a href="'+node.url+'">'+node.text+'</a>' );
		}
      }
      openlinkrichtext<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'tree', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setlinkrichtext'.$id), 'format', true) ?>", 400, 300);
      }
      setfilerichtext<?php echo $id ?> = function(url) {
      	if (url.length > 0) {
			var oEditor = FCKeditorAPI.GetInstance('<?php echo $name ?>');
			oEditor.InsertHtml( '<a href="'+url+'">'+url+'</a>' );
		}
      }
      openfilerichtext<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setfilerichtext'.$id), 'format', true) ?>", 800, 600);
      }
        <?php
        $this->view->headScript()->captureEnd();
        $button = '<input type="button" value="';
        $button .= $this->view->_('Add picture');
        $button .= '" onclick="openimagerichtext' . $id;
        $button .= '()"/>';
        $button .= '<input type="button" value="';
        $button .= $this->view->_('Add link');
        $button .= '" onclick="openlinkrichtext' . $id;
        $button .= '()"/>';
        $button .= '<input type="button" value="';
        $button .= $this->view->_('Add file');
        $button .= '" onclick="openfilerichtext' . $id;
        $button .= '()"/>';
        return $button . $this->formTextarea($name, $value, $attribs);
    }
}
