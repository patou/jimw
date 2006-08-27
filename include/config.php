<?php
class config {
  var $config_tab = array();
  var $rubrique = 'site';
  /*
   * config(string rubrique)
   * constructeur
   */
  function config($rubrique){
	$this->rubrique = $rubrique;
	$this->getconfig();
  }

  function getconfig(){
  	global $sql;
    //$this->sql = parent::getInstance();
    $req = 'SELECT DISTINCT nom, valeur FROM config WHERE rubrique LIKE \''.$this->rubrique.'\' OR rubrique LIKE \'site\' ';
    $result = $sql->doQuery($req);
    while (list($nom, $valeur) = $sql->fetch_array($result)) {
      //$this->config_tab[$nom] = @unserialize(stripslashes($valeur));
	  	 $this->config_tab[$nom] = $valeur;
    }
    //return $this->config_tab;
  }
  
  function get($nom){
    if (isset($this->config_tab[$nom])) {
      return $this->config_tab[$nom];
    }
    else
      return '';
  }
  
  function setconfig($nom, $valeur){
  global $sql;
	 $valeur = mysql_escape_string ($valeur);
    if (isset($this->config_tab[$nom]))
      $req = "UPDATE config
            SET valeur = '" . $valeur . "' " . " WHERE nom LIKE '" . $nom . "' AND rubrique LIKE '".$this->rubrique."'";
    else
    {
      $res = $sql->doQuery("SELECT max(id) + 1 AS max FROM config");
      $id = $sql->fetch($res);
      $req = "INSERT INTO config ( id , rubrique , nom , valeur  )
    VALUES ( '".$id->max. "', '".$this->rubrique."' , '" . $nom . "' , '" . $valeur . "'  ) ";
    }
    $sql->doQuery($req);
   //$this->config_tab[$nom] = @unserialize(stripslashes($valeur));
	  	 $this->config_tab[$nom] = $valeur;
  }
  function ajouter($nom, $valeur){
    $this->setconfig($nom, $valeur);
  }
  function modifier($nom, $valeur){
    $this->setconfig($nom,$valeur);
  }
}
?>