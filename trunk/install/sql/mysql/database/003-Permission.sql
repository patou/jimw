DROP TABLE IF EXISTS `{PREFIX}perm`;
DROP TABLE IF EXISTS `{PREFIX}siteperm`;
CREATE TABLE IF NOT EXISTS `{PREFIX}siteperm` (
  `siteperm_id` int(10) unsigned NOT NULL auto_increment,
  `site_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `siteperm_perm` text NOT NULL,
  PRIMARY KEY  (`siteperm_id`),
  UNIQUE KEY `site_id` (`site_id`,`group_id`)
);
UPDATE  `{PREFIX}group` SET  `group_name` =  'Administrators' WHERE  `group_id` = 3 LIMIT 1 ;
UPDATE  `{PREFIX}group` SET  `group_name` =  'Members' WHERE  `group_id` = 2 LIMIT 1 ;
UPDATE  `{PREFIX}group` SET  `group_name` =  'Guests' WHERE  `group_id` = 1 LIMIT 1 ;
