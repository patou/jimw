ALTER TABLE `jimw_domain` ADD `domain_path` VARCHAR( 255 ) NOT NULL ,
ADD `domain_status` TINYINT( 4 ) NOT NULL;
ALTER TABLE `jimw_domain` ADD `site_id` INT NOT NULL AFTER `database_id` ;