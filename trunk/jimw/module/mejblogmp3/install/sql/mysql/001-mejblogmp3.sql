CREATE TABLE IF NOT EXISTS `{PREFIX}blogmp3comment` (
  `blogmp3comment_id` int(11) NOT NULL auto_increment,
  `blogmp3message_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `blogmp3comment_rating` tinyint NOT NULL,
  `blogmp3comment_username` varchar(127) NOT NULL default '',
  `blogmp3comment_content` text NOT NULL,
  `blogmp3comment_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`blogmp3comment_id`),
  KEY `blogmp3message_id` (`blogmp3message_id`)
);

CREATE TABLE IF NOT EXISTS `{PREFIX}blogmp3message` (
  `blogmp3message_id` int(11) NOT NULL auto_increment,
  `tree_parentid` int(11) NOT NULL default '0',
  `tree_id` int(11) default NULL,
  `blogmp3message_title` varchar(255) NOT NULL default '',
  `blogmp3message_file` varchar(255) NOT NULL,
  `blogmp3message_content` text NOT NULL,
  `blogmp3message_name` varchar(50) NOT NULL,
  `blogmp3message_firstname` varchar(50) NOT NULL,
  `blogmp3message_email` varchar(100) NOT NULL,
  `blogmp3message_phone` varchar(25) NOT NULL,
  `blogmp3message_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `blogmp3message_content` BOOL NOT NULL DEFAULT '1',
  `blogmp3message_published` BOOL NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`blogmp3message_id`),
  KEY `tree_parentid` (`tree_parentid`)
);
