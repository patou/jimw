CREATE TABLE `{PREFIX}user` (
`user_id` INTEGER auto_increment ,
`user_login` VARCHAR (32)  NOT NULL default '',
`user_password` VARCHAR (32)  NOT NULL default '',
`user_status` TINYINT NOT NULL default '0',
`user_lastname` VARCHAR (32)  NOT NULL default '',
`user_firstname` VARCHAR (32)  NOT NULL default '',
`user_email` VARCHAR (32)  NOT NULL default '',
`user_creationdate` DATETIME NOT NULL default '1900-01-01 00:00:00',
`user_lastvisit` DATETIME NOT NULL default '1900-01-01 00:00:00',
PRIMARY KEY (`user_id`)
);

CREATE TABLE `{PREFIX}site` (
`site_id` INTEGER auto_increment ,
`tree_id` INTEGER default '0',
`site_access` TINYINT NOT NULL default '0',
`site_name` VARCHAR (64)  NOT NULL default 'site_domaine',
`site_path` VARCHAR (255)  NOT NULL default '',
PRIMARY KEY (`site_id`)
);

CREATE TABLE `{PREFIX}domain` (
`domain_id` INTEGER auto_increment ,
`site_id` INTEGER default '0',
`domain_protocol` VARCHAR (8)  NOT NULL default '',
`domain_name` VARCHAR (255)  NOT NULL default '',
`domain_subdomain` VARCHAR (255)  NOT NULL default '',
`domain_port` VARCHAR (32)  NOT NULL default '',
`domain_path` VARCHAR (255)  NOT NULL default '',
`domain_status` TINYINT NOT NULL default '0',
PRIMARY KEY (`domain_id`)
);

CREATE TABLE `{PREFIX}tree` (
`tree_id` INTEGER auto_increment ,
`tree_parentid` INTEGER default '0',
`tree_order` INTEGER NOT NULL default '0',
`site_id` INTEGER default '0',
`module_id` INTEGER default '0',
`tree_pagetitle` VARCHAR (255)  NOT NULL default '',
`tree_menutitle` VARCHAR (32)  NOT NULL default '',
`tree_icon` VARCHAR (127)  NOT NULL default '',
`tree_image` VARCHAR (127)  NOT NULL default '',
`tree_alias` VARCHAR (255)  NOT NULL default '',
`tree_description` MEDIUMTEXT NOT NULL default '',
`user_id` INTEGER default '0',
`tree_status` TINYINT NOT NULL default '0',
`tree_version` INTEGER NOT NULL default '0',
`tree_type` TINYINT NOT NULL default '0',
`tree_param` TINYTEXT  NOT NULL default '',
`tree_creationdate` DATETIME NOT NULL default '1900-01-01 00:00:00',
`tree_editiondate` DATETIME NOT NULL default '1900-01-01 00:00:00',
PRIMARY KEY (`tree_id`)
);

CREATE TABLE `{PREFIX}menuitem` (
`menuitem_id` INTEGER auto_increment ,
`menu_name` VARCHAR (32)  NOT NULL default '',
`site_id` INTEGER default '0',
`tree_id` INTEGER default '0',
`menuitem_order` INTEGER NOT NULL default '0',
`menuitem_status` INTEGER NOT NULL default '0',
`menuitem_level` TINYINT NOT NULL default '0',
PRIMARY KEY (`menuitem_id`)
);

CREATE TABLE `{PREFIX}module` (
`module_id` INTEGER auto_increment ,
`module_name` VARCHAR (32)  NOT NULL default '',
`module_path` VARCHAR (32)  NOT NULL default '',
`module_author` VARCHAR (64)  NOT NULL default '',
`module_version` VARCHAR (16)  NOT NULL default '',
`module_comment` VARCHAR (255)  NOT NULL default '',
PRIMARY KEY (`module_id`)
);

CREATE TABLE `{PREFIX}module_config` (
`module_config_id` INTEGER auto_increment ,
`module_id` INTEGER default '0',
`module_config_key` VARCHAR (32)  NOT NULL default '',
`module_config_value` MEDIUMTEXT NOT NULL default '',
PRIMARY KEY (`module_config_id`),
KEY `module_config_key` (`module_config_key`)
);

CREATE TABLE `{PREFIX}article` (
`article_id` INTEGER auto_increment ,
`tree_id` INTEGER default '0',
`user_id` INTEGER default '0',
`validator_id` INTEGER default '0',
`article_date` DATETIME NOT NULL default '1900-01-01 00:00:00',
`article_status` TINYINT NOT NULL default '0',
`article_revisioninfo` VARCHAR (255)  NOT NULL default '',
`article_content` MEDIUMTEXT NOT NULL default '',
PRIMARY KEY (`article_id`)
);

