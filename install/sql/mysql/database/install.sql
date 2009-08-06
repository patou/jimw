CREATE TABLE `{PREFIX}group` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `group_name` varchar(64) NOT NULL,
  `group_type` int(11) NOT NULL DEFAULT '0',
  `group_parents` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`group_id`)
);
CREATE TABLE `{PREFIX}module` (
  `module_id` int(11) NOT NULL auto_increment,
  `module_name` varchar(32) NOT NULL DEFAULT '',
  `module_path` varchar(32) NOT NULL DEFAULT '',
  `module_author` varchar(64) NOT NULL DEFAULT '',
  `module_version` varchar(16) NOT NULL DEFAULT '',
  `module_comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`module_id`)
);
CREATE TABLE `{PREFIX}moduleconfig` (
  `moduleconfig_id` int(11) NOT NULL auto_increment,
  `module_id` int(11) DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `moduleconfig_config` text,
  PRIMARY KEY  (`moduleconfig_id`),
  KEY `module_config_key` (`site_id`)
);
CREATE TABLE `{PREFIX}site` (
  `site_id` int(11) NOT NULL auto_increment,
  `tree_id` int(11) NOT NULL DEFAULT '0',
  `default_tree_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL DEFAULT '1',
  `site_parentid` int(11) NOT NULL DEFAULT '0',
  `site_access` tinyint(4) NOT NULL DEFAULT '0',
  `site_name` varchar(64) NOT NULL DEFAULT 'site_domaine',
  `site_path` varchar(255) NOT NULL DEFAULT '',
  `site_template` varchar(255) NOT NULL,
  PRIMARY KEY  (`site_id`)
);
CREATE TABLE `{PREFIX}siteperm` (
  `siteperm_id` int(10) unsigned NOT NULL auto_increment,
  `site_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `siteperm_perm` text NOT NULL,
  PRIMARY KEY  (`siteperm_id`),
  UNIQUE KEY `site_id` (`site_id`,`group_id`)
);
CREATE TABLE `{PREFIX}tree` (
  `tree_id` int(11) NOT NULL auto_increment,
  `tree_parentid` int(11) NOT NULL DEFAULT '0',
  `tree_lft` int(11) NOT NULL,
  `tree_rgt` int(11) NOT NULL,
  `tree_order` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  `module_path` varchar(32) NOT NULL,
  `tree_pagetitle` varchar(255) NOT NULL DEFAULT '',
  `tree_menutitle` varchar(32) NOT NULL DEFAULT '',
  `tree_icon` varchar(127) NOT NULL DEFAULT '',
  `tree_image` varchar(127) NOT NULL DEFAULT '',
  `tree_alias` varchar(255) NOT NULL DEFAULT '',
  `tree_description` mediumtext,
  `user_id` int(11) DEFAULT '0',
  `tree_status` tinyint(4) NOT NULL DEFAULT '0',
  `tree_version` int(11) NOT NULL DEFAULT '0',
  `tree_type` tinyint(4) NOT NULL DEFAULT '0',
  `tree_param` tinytext,
  `tree_creationdate` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `tree_editiondate` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  PRIMARY KEY  (`tree_id`)
);
CREATE TABLE `{PREFIX}user` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_login` varchar(32) NOT NULL DEFAULT '',
  `user_password` varchar(32) NOT NULL DEFAULT '',
  `user_status` tinyint(4) NOT NULL DEFAULT '0',
  `user_lastname` varchar(32) NOT NULL DEFAULT '',
  `user_firstname` varchar(32) NOT NULL DEFAULT '',
  `user_email` varchar(32) NOT NULL DEFAULT '',
  `user_creationdate` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `user_lastvisit` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `user_information` text NOT NULL,
  PRIMARY KEY  (`user_id`)
);
CREATE TABLE `{PREFIX}usergroup` (
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`group_id`,`user_id`)
);
INSERT INTO `{PREFIX}group` (`group_id`, `group_name`, `group_type`, `group_parents`) VALUES(1, 'Guests', 1, NULL);
INSERT INTO `{PREFIX}group` (`group_id`, `group_name`, `group_type`, `group_parents`) VALUES(2, 'Members', 1, NULL);
INSERT INTO `{PREFIX}group` (`group_id`, `group_name`, `group_type`, `group_parents`) VALUES(3, 'Administrators', 1, NULL);