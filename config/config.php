<?php
if (!is_file('.htaccess.dist'))
{
define("SQL_TYPE", "mysql");
define("SQL_HOST", "localhost");
define("SQL_USER", "root");
define("SQL_PASS", "");
define("SQL_DB", "hautefeuille");
define("URL_ROOT", "http://localhost/hautefeuille/");
}
else
{
define("SQL_TYPE", "mysql");
define("SQL_HOST", "db500.unetun.com");
define("SQL_USER", "dbo155433403");
define("SQL_PASS", "kWx5pJ7A");
define("SQL_DB", "db155433403");
define("URL_ROOT", "http://www.hautefeuille.org/");
}
?>
