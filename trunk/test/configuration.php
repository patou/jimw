<?php

if (isset ($jimw_config_db))
Zend_Registry::set('config_db', $jimw_config_db);
else
Zend_Registry::set('config_db', array('type' => 'PDO_SQLITE', 'dbname' => JIMW_REP . 'config/config.db'));
Zend_Registry::set('db_prefix', 'jimw');
$db_param = array ('dbname' => 'jimw',
	'host' => 'localhost',
	'username' => 'root',
	'password' => '');
try {
	$db = Zend_Db::factory('PDO_MYSQL', $db_param);
	$db->getConnection();
	Zend_Db_Table::setDefaultAdapter($db);
	Zend_Registry::set('db', $db);
	Zend_Registry::set('db_prefix', 'jimw');
} catch (Zend_Db_Adapter_Exception $e){
	echo $e->getMessage();
	throw new Jimw_Global_Exception('Could not connect to the database');
}