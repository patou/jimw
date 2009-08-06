CREATE TABLE IF NOT EXISTS `{PREFIX}article` (
`article_id` INTEGER auto_increment ,
`tree_id` INTEGER default '0',
`user_id` INTEGER default '0',
`validator_id` INTEGER default '0',
`article_date` DATETIME NOT NULL default '1900-01-01 00:00:00',
`article_status` TINYINT NOT NULL default '0',
`article_version` INTEGER NOT NULL default '0',
`article_revisioninfo` VARCHAR (255)  NOT NULL default '',
`article_content` MEDIUMTEXT NOT NULL,
PRIMARY KEY (`article_id`)
);

