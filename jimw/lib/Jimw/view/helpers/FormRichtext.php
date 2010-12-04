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
        $options = array('width' => 600, 'height' => 400, 'toolbarSet' => 'Full');
        if (isset($attribs['editor'])) {
            $options = array_merge($options ,$attribs['editor']);
            unset($attribs['editor']);
        }
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/javascripts/jimw.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->path . '/ckeditor/ckeditor.js');
        $this->view->headScript()->captureStart();
        $id = str_replace('-', '_',$this->_normalizeId($name));
        ?>
      addOnloadScript(function()
      {
        var oFCKeditor = CKEDITOR.replace( '<?php echo $name ?>', {
        width: '<?php echo $options['width'] ?>',
        height: '<?php echo $options['height'] ?>',
        toolbar: '<?php echo $options['toolbarSet'] ?>',
        filebrowserBrowseUrl : '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax'), 'format', true) ?>',
        filebrowserUploadUrl : '<?php echo $this->view->url(array('controller' => 'file', 'action' => 'upload2', 'format' => 'ajax'), 'format', true) ?>'
        }) ;

      });
      setimagerichtext<?php echo $id ?> = function(url) {
      	if (url.length > 0) {
			var oEditor = CKEDITOR.instances.<?php echo $name ?>;
			oEditor.insertHtml( '<img src="'+url+'" />' );
		}
      }
      openimagerichtext<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setimagerichtext'.$id), 'format', true) ?>", 800, 600);
      }
      setlinkrichtext<?php echo $id ?> = function(node) {
      	if (node) {
			var oEditor = CKEDITOR.instances.<?php echo $name ?>;
			var text = node.name ? node.name : node.text;
			oEditor.insertHtml( ' <a href="'+node.url+'">'+text+'</a> ' );
		}
      }
      openlinkrichtext<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'tree', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setlinkrichtext'.$id, 'namefield' => true), 'format', true) ?>", 400, 450);
      }
      setfilerichtext<?php echo $id ?> = function(url, name) {
      	if (url.length > 0) {
			var oEditor = CKEDITOR.instances.<?php echo $name ?>;
			var text = name ? name : url;
			oEditor.insertHtml( ' <a href="'+url+'">'+text+'</a> ' );
		}
      }
      openfilerichtext<?php echo $id ?> = function(url) {
			openWindow("<?php echo $this->view->url(array('controller' => 'file', 'action' => 'chooser', 'format' => 'ajax', 'javascript_fct' => 'setfilerichtext'.$id, 'namefield' => true), 'format', true) ?>", 800, 600);
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
        $button .= '()"/><br />';
        return $button . $this->formTextarea($name, $value, $attribs);
    }
}
