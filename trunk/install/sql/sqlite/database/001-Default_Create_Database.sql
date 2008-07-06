CREATE TABLE "{PREFIX}group" ( "group_id" INTEGER NOT NULL PRIMARY KEY, "group_name" varchar(64) NOT NULL , "group_type" int(11) NOT NULL default '0' , "group_parents" varchar(255) default NULL );

CREATE TABLE "{PREFIX}menuitem" ( "menuitem_id" INTEGER NOT NULL PRIMARY KEY, "menu_name" varchar(32) NOT NULL default '' , "site_id" int(11) default '0' , "tree_id" int(11) default '0' , "menuitem_order" int(11) NOT NULL default '0' , "menuitem_status" int(11) NOT NULL default '0' , "menuitem_level" tinyint(4) NOT NULL default '0' );

CREATE TABLE "{PREFIX}module" ( "module_id" INTEGER NOT NULL PRIMARY KEY, "module_name" varchar(32) NOT NULL default '' , "module_path" varchar(32) NOT NULL default '' , "module_author" varchar(64) NOT NULL default '' , "module_version" varchar(16) NOT NULL default '' , "module_comment" varchar(255) NOT NULL default '' );

CREATE TABLE "{PREFIX}module_config" ( "module_config_id" INTEGER NOT NULL PRIMARY KEY, "module_id" int(11) default '0' , "module_config_key" varchar(32) NOT NULL default '' , "module_config_value" mediumtext NOT NULL );

CREATE TABLE "{PREFIX}path" ( "path_id" INTEGER NOT NULL PRIMARY KEY, "site_id" int(11) default '0' , "path_protocol" varchar(8) NOT NULL , "path_name" varchar(255) NOT NULL , "path_subdomain" varchar(255) NOT NULL , "path_port" varchar(32) NOT NULL , "path_path" varchar(255) NOT NULL , "path_status" tinyint(4) NOT NULL default '0' );

CREATE TABLE "{PREFIX}perm" ( "perm_id" INTEGER NOT NULL PRIMARY KEY, "siteperm_id" int(11) NOT NULL , "perm_name" varchar(255) NOT NULL , "perm_allow" tinyint(4) NOT NULL default '0' , "perm_type" tinyint(4) NOT NULL default '0' );

CREATE TABLE "{PREFIX}schema" ( "schema_id" INTEGER NOT NULL PRIMARY KEY, "schema_module" varchar(32) NOT NULL default '' , "schema_version" int(11) NOT NULL default '0' );

CREATE TABLE "{PREFIX}site" ( "site_id" INTEGER NOT NULL PRIMARY KEY, "tree_id" int(11) default '0' , "site_access" tinyint(4) NOT NULL default '0' , "site_name" varchar(64) NOT NULL default 'site_domaine' , "site_path" varchar(255) NOT NULL default '' );

CREATE TABLE "{PREFIX}siteperm" ( "siteperm_id" INTEGER NOT NULL PRIMARY KEY, "site_id" int(11) NOT NULL , "group_id" int(11) NOT NULL , "siteperm_allow" tinyint(4) NOT NULL default '0' , "siteperm_admin" tinyint(4) NOT NULL default '0' );

CREATE TABLE "{PREFIX}tree" ( "tree_id" INTEGER NOT NULL PRIMARY KEY, "tree_parentid" int(11) default '0' , "tree_lft" int(11) NOT NULL , "tree_rgt" int(11) NOT NULL , "tree_order" int(11) NOT NULL default '0' , "site_id" int(11) default '0' , "module_id" int(11) default '0' , "tree_pagetitle" varchar(255) NOT NULL default '' , "tree_menutitle" varchar(32) NOT NULL default '' , "tree_icon" varchar(127) NOT NULL default '' , "tree_image" varchar(127) NOT NULL default '' , "tree_alias" varchar(255) NOT NULL default '' , "tree_description" mediumtext NOT NULL , "user_id" int(11) default '0' , "tree_status" tinyint(4) NOT NULL default '0' , "tree_version" int(11) NOT NULL default '0' , "tree_type" tinyint(4) NOT NULL default '0' , "tree_param" tinytext NOT NULL , "tree_creationdate" datetime NOT NULL default '1900-01-01 00:00:00' , "tree_editiondate" datetime NOT NULL default '1900-01-01 00:00:00' );

CREATE TABLE "{PREFIX}user" ( "user_id" INTEGER NOT NULL PRIMARY KEY, "user_login" varchar(32) NOT NULL default '' , "user_password" varchar(32) NOT NULL default '' , "user_status" tinyint(4) NOT NULL default '0' , "user_lastname" varchar(32) NOT NULL default '' , "user_firstname" varchar(32) NOT NULL default '' , "user_email" varchar(32) NOT NULL default '' , "user_creationdate" datetime NOT NULL default '1900-01-01 00:00:00' , "user_lastvisit" datetime NOT NULL default '1900-01-01 00:00:00' );

CREATE TABLE "{PREFIX}usergroup" ( "usergroup_id" INTEGER NOT NULL PRIMARY KEY, "group_id" int(10) NOT NULL , "user_id" int(10) NOT NULL );
