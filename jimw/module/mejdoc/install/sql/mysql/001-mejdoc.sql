CREATE TABLE IF NOT EXISTS `{PREFIX}mejdoc` (
  `mejdoc_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `tree_id` INT NOT NULL ,
  `mejdoc_title` VARCHAR( 255 ) NOT NULL ,
  `mejdoc_comment` TEXT NOT NULL ,
  `mejdoc_file` VARCHAR( 255 ) NOT NULL ,
  `mejdoc_editiondate` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `mejdoc_publishdate` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `mejdoc_order` INT NOT NULL ,
  INDEX ( `tree_id` )
);