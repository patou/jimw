<?php
class DBTools {
     var $_connection = false;
     var $_erreur_report = True;
     var $_host = "localhost";
     var $_user = "root";
     var $_pass = "";
     var $_db = "";

   function DBTools ($host = "localhost", $user = "root", $pass = "", $db = ""){
      $this->_host = $host;
      $this->_user = $user;
      $this->_pass = $pass;
      $this->_db = $db;
   }
   function connect (){
      if (!$this->_connection = @mysql_connect($this->_host, $this->_user, $this->_pass))
        echo "Connection impossible à la base";
      else
        if (!@mysql_select_db($this->_db, $this->_connection))
          echo "Selection impossible de la base";
        else
          return true;
      echo " ", $this->Error();
      return false;
   }
     function disconnect (){
   	  if ($this->_connection) {
   	    @mysql_close ($this->_connection);
        $this->_connection = false;
	    }
   }
     function doQuery ($query){
      $this->_connectIfNeeded ();
      if (!$result = @mysql_query ($query, $this->_connection) AND $this->_erreur_report)
         echo 'Impossible d\'executer la requette ', $query, ' ', $this->Error();
      return $result;
   }
     function _connectIfNeeded (){
      if (!$this->_connection)
         if (! ($this->connect()) AND $this->_erreur_report)
            echo ('Impossible de se connecter &agrav; la base '.$this->_dbPath);
   }
     function fetch ($rs){
      return @mysql_fetch_object($rs);
   }
     function fetch_array ($rs){
      return @mysql_fetch_array($rs);
   }
     function numRow ($rs){
   	  return @mysql_num_rows($rs);
   }
     function Error() {
      return @mysql_error();
  }
}
?>
