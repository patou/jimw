<?php
interface jimw_class_data()
{
	public :
		var $name;
		var $lang;
		var	$table;
		
		function __tostring();
		function __construct();
		function __destruct();
		function delete();
		function scan();
		function display();
		function load();
		function save();
}

interface jimw_class_data_text extends jimw_class_data()
{
	public :
		var $name;
		var $lang;
		var	$table;
		
		function __tostring();
		function __construct();
		function __destruct();
		function delete();
		function scan();
		function display();
		function load();
		function save();
};

interface jimw_class_data_file extends jimw_class_data()
{
	public :
		var $name;
		var $lang;
		var	$table;
		
		function __tostring();
		function __construct();
		function __destruct();
		function dowmload();
		function delete();
		function scan();
		function display();
		function load();
		function save();
};

interface jimw_class_data_media extends jimw_class_data()
{
	public : 
		var $name;
		var $lang;
		var	$table;
		
		function __tostring();
		function __construct();
		function __destruct();
		function delete();
		function scan();
		function display();
		function load();
		function save();
};


?>

