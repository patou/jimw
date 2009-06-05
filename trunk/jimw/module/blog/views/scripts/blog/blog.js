<?php
$response = Zend_Controller_Front::getInstance()->getResponse();
$response->setHeader('Content-Type', 'text/javascript; charset: UTF-8');
//$response->setHeader('Content-disposition', 'inline; filename='.$this->tree->alias.'.pdf');
if (count($this->messages) > 0) {
  echo "document.write ('<ul>');\n";
  //$url = $this->url(array('alias' => $this->tree->alias, 'ext' => 'phtml'), 'alias', true);
  foreach ($this->messages as $message) {
    $tree = new Jimw_Site_Tree();
    $result = $tree->find($message->tree_parentid)->current();
    $url = $this->url(array('alias' => $result->alias, 'ext' => 'phtml'), 'alias', true, true);
    echo "document.write ('<li><a href=\"".$url."#".$message->id."\">".str_replace("'","\'",$message->title)."</a></li>');\n";
  }
  echo "document.write ('</ul>');\n";
}
