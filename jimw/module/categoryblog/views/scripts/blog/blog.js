<?php
$response = Zend_Controller_Front::getInstance()->getResponse();
$response->setHeader('Content-Type', 'text/javascript; charset: UTF-8');
//$response->setHeader('Content-disposition', 'inline; filename='.$this->tree->alias.'.pdf');
if (count($this->messages) > 0) {
  echo "document.write ('<ul>');\n";
  //$url = $this->url(array('alias' => $this->tree->alias, 'ext' => 'phtml'), 'alias', true);
  foreach ($this->messages as $message) {
    //$url = $this->url(array('alias' => $message->tree_alias, 'ext' => 'phtml'), 'alias', true, true);
	  $url = $this->url(array('module' => 'blog', 'controller' => 'message', 'action' => 'view', 'format' => 'phtml', 'alias' => $message->tree_alias, 'mid' => $message->id), 'format');
    echo "document.write ('<li><a href=\"".$url."\">".str_replace("'","\'",$message->title)."</a></li>');\n";
  }
  echo "document.write ('</ul>');\n";
}
