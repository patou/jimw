<?php
class DBTools {
   private $_connection = null;
   private $_dbPath = null;
   private $_erreur_report = True;

   public function __construct ($host = "", $user = "", $pass = "", $db = "base.db"){
     $this->_dbPath = $db;
   }
   function __desctruct () {
      $this->disconnect ();
   }
   public function connect (){
      if ($this->_connection === null)
         $this->_connection = @sqlite_open ($this->_dbPath);
   }
   public function disconnect (){
   	if ($this->_connection != null) {
   	  @sqlite_close ($this->_connection);
      $this->_connection = null;
	    }
   }
   public function doQuery ($query){
      $this->_connectIfNeeded ();
      if (!$result = @sqlite_query ($query, $this->_connection) AND $this->_erreur_report)
         echo 'Impossible d\'executer la requette ', $query, ' ', $this->Error();
      return $result;
   }
   private function _connectIfNeeded (){
      if ($this->_connection === null)
         if (! ($this->_connection = @sqlite_open ($this->_dbPath)) AND $this->_erreur_report)
            throw new Exception ('Impossible de se connecter &agrav; la base '.$this->_dbPath);
   }
   public function fetch ($rs){
      return @sqlite_fetch_object($rs);
   }
   public function fetch_array ($rs){
      return @sqlite_fetch_array($rs);
   }
   public function numRow ($rs){
   	  return @sqlite_num_rows($rs);
   }
   public function Error() {
      if (@sqlite_last_error($this->_connection) AND $this->_erreur_report)
        return @sqlite_error_string(@sqlite_last_error($this->_connection));//$this->lastError()
  }
}
?>
