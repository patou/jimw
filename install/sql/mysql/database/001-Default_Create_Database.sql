CREATE TABLE IF NOT EXISTS `{PREFIX}user` (
`user_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`user_login` VARCHAR (32)  NOT NULL DEFAULT '',
`user_password` VARCHAR (32)  NOT NULL DEFAULT '',
`user_status` TINYINT NOT NULL DEFAULT '0',
`user_lastname` VARCHAR (32)  NOT NULL DEFAULT '',
`user_firstname` VARCHAR (32)  NOT NULL DEFAULT '',
`user_email` VARCHAR (32)  NOT NULL DEFAULT '',
`user_creationdate` DATETIME NOT NULL DEFAULT '1900-01-01 00:00:00',
`user_lastvisit` DATETIME NOT NULL DEFAULT '1900-01-01 00:00:00'
);

CREATE TABLE IF NOT EXISTS `{PREFIX}site` (
`site_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`tree_id` INTEGER DEFAULT '0',
`site_access` TINYINT NOT NULL DEFAULT '0',
`site_name` VARCHAR (64)  NOT NULL DEFAULT 'site_domaine',
`site_path` VARCHAR (255)  NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS `{PREFIX}tree` (
`tree_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`tree_parentid` INTEGER NOT NULL DEFAULT '0',
`tree_lft` INT NOT NULL,
`tree_rgt` INT NOT NULL,
`tree_order` INTEGER NOT NULL DEFAULT '0',
`site_id` INTEGER NOT NULL DEFAULT '0',
`module_id` INTEGER NOT NULL DEFAULT '0',
`tree_pagetitle` VARCHAR (255) NOT NULL DEFAULT '',
`tree_menutitle` VARCHAR (32) NOT NULL DEFAULT '',
`tree_icon` VARCHAR (127) NOT NULL DEFAULT '',
`tree_image` VARCHAR (127) NOT NULL DEFAULT '',
`tree_alias` VARCHAR (255) NOT NULL DEFAULT '',
`tree_description` MEDIUMTEXT NOT NULL DEFAULT '',
`user_id` INTEGER NULL DEFAULT '0',
`tree_status` TINYINT NOT NULL DEFAULT '0',
`tree_version` INTEGER NOT NULL DEFAULT '0',
`tree_type` TINYINT NOT NULL DEFAULT '0',
`tree_param` TINYTEXT NOT NULL DEFAULT '',
`tree_creationdate` DATETIME NOT NULL DEFAULT '1900-01-01 00:00:00',
`tree_editiondate` DATETIME NOT NULL DEFAULT '1900-01-01 00:00:00'
);

CREATE TABLE IF NOT EXISTS `{PREFIX}menuitem` (
`menuitem_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`menu_name` VARCHAR (32)  NOT NULL DEFAULT '',
`site_id` INTEGER DEFAULT '0',
`tree_id` INTEGER DEFAULT '0',
`menuitem_order` INTEGER NOT NULL DEFAULT '0',
`menuitem_status` INTEGER NOT NULL DEFAULT '0',
`menuitem_level` TINYINT NOT NULL DEFAULT '0'
);

CREATE TABLE IF NOT EXISTS `{PREFIX}module` (
`module_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`module_name` VARCHAR (32)  NOT NULL DEFAULT '',
`module_path` VARCHAR (32)  NOT NULL DEFAULT '',
`module_author` VARCHAR (64)  NOT NULL DEFAULT '',
`module_version` VARCHAR (16)  NOT NULL DEFAULT '',
`module_comment` VARCHAR (255)  NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS `{PREFIX}module_config` (
`module_config_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`module_id` INTEGER DEFAULT '0',
`module_config_key` VARCHAR (32)  NOT NULL DEFAULT '',
`module_config_value` MEDIUMTEXT NOT NULL DEFAULT '',
KEY `module_config_key` (`module_config_key`)
);

CREATE TABLE IF NOT EXISTS `{PREFIX}group` (
	`group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`group_name` VARCHAR( 64 ) NOT NULL ,
	`group_type` INT NOT NULL DEFAULT '0',
	`group_parents` VARCHAR( 255 ) NULL
);

INSERT INTO `{PREFIX}group` (group_id, group_name, group_type, group_parents) VALUES
	(1, 'Gest', 1, NULL),
	(2, 'Members', 1, NULL),
	(3, 'Administrator', 1, NULL);

CREATE TABLE IF NOT EXISTS `{PREFIX}usergroup` (
	`group_id` INT UNSIGNED NOT NULL ,
	`user_id` INT UNSIGNED NOT NULL,
 	PRIMARY KEY  (`group_id`,`user_id`)
);

CREATE TABLE IF NOT EXISTS `{PREFIX}siteperm` (
	`siteperm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`site_id` INT NOT NULL ,
	`group_id` INT NOT NULL ,
	`siteperm_allow` TINYINT NOT NULL DEFAULT '0',
	`siteperm_admin` TINYINT NOT NULL DEFAULT '0',
	UNIQUE  (`site_id`,`group_id`)
);

CREATE TABLE IF NOT EXISTS `{PREFIX}perm` (
	`perm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`siteperm_id` INT NOT NULL ,
	`perm_name` VARCHAR( 255 ) NOT NULL ,
	`perm_allow` TINYINT NOT NULL DEFAULT '0',
	`perm_type` TINYINT NOT NULL DEFAULT '0'
);
