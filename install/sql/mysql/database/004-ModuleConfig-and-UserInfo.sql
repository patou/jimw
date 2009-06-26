ALTER TABLE  `{PREFIX}user` ADD  `user_information` TEXT NULL ;
ALTER TABLE  `{PREFIX}moduleconfig` CHANGE  `module_config_id`  `moduleconfig_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE  `module_config`  `moduleconfig_config` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;