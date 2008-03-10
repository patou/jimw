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
CREATE TABLE `{PREFIX}usergroup` (
	`group_id` INT UNSIGNED NOT NULL ,
	`user_id` INT UNSIGNED NOT NULL,
 	PRIMARY KEY  (`group_id`,`user_id`)
);
CREATE TABLE `{PREFIX}siteperm` (
	`siteperm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`site_id` INT NOT NULL ,
	`group_id` INT NOT NULL ,
	`siteperm_allow` TINYINT NOT NULL DEFAULT '0',
	`siteperm_admin` TINYINT NOT NULL DEFAULT '0',
	UNIQUE  (`site_id`,`group_id`)
);
CREATE TABLE `{PREFIX}perm` (
	`perm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`siteperm_id` INT NOT NULL ,
	`perm_name` VARCHAR( 255 ) NOT NULL ,
	`perm_allow` TINYINT NOT NULL DEFAULT '0',
	`perm_type` TINYINT NOT NULL DEFAULT '0'
);