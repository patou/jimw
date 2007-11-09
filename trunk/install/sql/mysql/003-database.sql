RENAME TABLE `{PREFIX}domain`  TO `{PREFIX}path`;
ALTER TABLE `{PREFIX}path` CHANGE `domain_id` `path_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE `domain_protocol` `path_protocol` VARCHAR( 8 ) NOT NULL ,
CHANGE `domain_name` `path_name` VARCHAR( 255 ) NOT NULL ,
CHANGE `domain_subdomain` `path_subdomain` VARCHAR( 255 ) NOT NULL ,
CHANGE `domain_port` `path_port` VARCHAR( 32 ) NOT NULL ,
CHANGE `domain_path` `path_path` VARCHAR( 255 ) NOT NULL ,
CHANGE `domain_status` `path_status` TINYINT( 4 ) NOT NULL DEFAULT '0';