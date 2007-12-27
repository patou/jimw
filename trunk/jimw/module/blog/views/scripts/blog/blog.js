<?php
$response = Zend_Controller_Front::getInstance()->getResponse();
$response->setHeader('Content-Type', 'text/javascript; charset: UTF-8');
//$response->setHeader('Content-disposition', 'inline; filename='.$this->tree->alias.'.pdf');
if (count($this->messages) > 0) {
  echo "document.write ('<ul>');\n";
  $url = $this->url(array('alias' => $this->tree->alias), 'alias', true);
  foreach ($this->messages as $message) {
    echo "document.write ('<li><a href=\"".$url."#".$message->id."\">".str_replace("'","\'",$message->title)."</a></li>');\n";
  }
  echo "document.write ('</ul>');\n";
}
?>