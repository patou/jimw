ALTER TABLE `{PREFIX}domain` ADD `domain_path` VARCHAR( 255 ) NOT NULL ,
ADD `domain_status` TINYINT( 4 ) NOT NULL;
ALTER TABLE `{PREFIX}domain` ADD `site_id` INT NOT NULL AFTER `database_id` ;
UPDATE `{PREFIX}domain` SET `site_id` = 1;