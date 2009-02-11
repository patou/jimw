ALTER TABLE  `{PREFIX}tree` ADD  `module_path` VARCHAR( 32 ) NOT NULL AFTER  `module_id` ;
RENAME TABLE  `{PREFIX}module_config` TO  `{PREFIX}moduleconfig`;
ALTER TABLE  `{PREFIX}moduleconfig` CHANGE  `module_config_key`  `site_id` INT( 11 ) NOT NULL ,
CHANGE  `module_config_value`  `module_config` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `{PREFIX}site` 
CHANGE `tree_id` `default_tree_id` INT(11) NOT NULL;
ALTER TABLE  `{PREFIX}site`
ADD  `tree_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER  `site_id`,
ADD  `domain_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER  `default_tree_id`,
ADD  `site_parentid` INT( 11 ) NOT NULL DEFAULT '0' AFTER  `domain_id`,
ADD  `site_template` VARCHAR( 255 ) NOT NULL AFTER  `site_path` ;
UPDATE `{PREFIX}site` SET `site_template` = CONCAT(site_path, '/template');
UPDATE `{PREFIX}tree` t SET t.`module_path` = (SELECT m.`module_path` FROM `{PREFIX}module` m WHERE m.`module_id` = t.`module_id`);