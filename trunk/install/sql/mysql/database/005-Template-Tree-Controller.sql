ALTER TABLE  `{PREFIX}tree` ADD  `tree_controller` varchar(255) NOT NULL DEFAULT '' AFTER `tree_alias`;
CREATE TABLE IF NOT EXISTS `{PREFIX}template` (
`template_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
`site_id` INTEGER DEFAULT '0',
`template_path` VARCHAR (255)  NOT NULL DEFAULT '',
`template_config` MEDIUMTEXT,
KEY `template_path` (`template_path`)
);