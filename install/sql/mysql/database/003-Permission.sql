DROP TABLE IF EXISTS `{PREFIX}perm`;
DROP TABLE IF EXISTS `{PREFIX}siteperm`;
CREATE TABLE IF NOT EXISTS `{PREFIX}siteperm` (
  `siteperm_id` int(10) unsigned NOT NULL auto_increment,
  `site_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `siteperm_perm` text NOT NULL default '',
  PRIMARY KEY  (`siteperm_id`),
  UNIQUE KEY `site_id` (`site_id`,`group_id`)
);
INSERT INTO `{PREFIX}usergroup` VALUES (1, 3);
