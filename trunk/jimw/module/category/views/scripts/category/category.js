<?php
$response = Zend_Controller_Front::getInstance()->getResponse();
$response->setHeader('Content-Type', 'text/javascript; charset: UTF-8');
//$response->setHeader('Content-disposition', 'inline; filename='.$this->tree->alias.'.pdf');
if (count($this->list_articles) > 0) {
  echo "document.write ('<ul>');\n";
  //$url = $this->url(array('alias' => $this->tree->alias, 'ext' => 'phtml'), 'alias', true);
  foreach ($this->list_articles as $article) {
    //$tree = new Jimw_Site_Tree();
    //$result = $tree->find($message->tree_parentid)->current();
    //$url = $this->url(array('alias' => $result->alias, 'ext' => 'phtml'), 'alias', true);
    $url="";
    echo "document.write ('<li><a href=\"".$url."#".$article->id."\">".str_replace("'","\'",$article->menutitle)."</a></li>');\n";
  }
  echo "document.write ('</ul>');\n";
}
?>