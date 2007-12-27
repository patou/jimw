CREATE TABLE `{PREFIX}blogmessage` (
`blogmessage_id` int(11) NOT NULL auto_increment,
`tree_parentid` int(11) NOT NULL default '0',
`tree_id` int(11) default NULL,
`blogmessage_title` varchar(255) NOT NULL default '',
`blogmessage_header` text NOT NULL,
`blogmessage_content` text NOT NULL,
`blogmessage_date` datetime NOT NULL default '0000-00-00 00:00:00',
`user_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`blogmessage_id`),
KEY `tree_parentid` (`tree_parentid`)
);

CREATE TABLE `{PREFIX}blogcomment` (
`blogcomment_id` int(11) NOT NULL auto_increment,
`blogmessage_id` int(11) NOT NULL default '0',
`user_id` int(11) default NULL,
`blogcomment_username` varchar(127) NOT NULL default '',
`blogcomment_content` text NOT NULL,
`blogcomment_date` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`blogcomment_id`),
KEY `blogmessage_id` (`blogmessage_id`)
);
