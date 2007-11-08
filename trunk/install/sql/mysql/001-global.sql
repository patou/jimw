CREATE TABLE `{PREFIX}admin` (
`admin_id` INTEGER auto_increment ,
`admin_login` VARCHAR (32)  NOT NULL default '',
`admin_pass` VARCHAR (40)  NOT NULL default '',
`admin_name` VARCHAR (64)  NOT NULL default '',
`admin_email` VARCHAR (100)  NOT NULL default '',
PRIMARY KEY (`admin_id`)
);

CREATE TABLE `{PREFIX}domain` (
`domain_id` INTEGER auto_increment ,
`database_id` INTEGER default '0',
`domain_protocol` VARCHAR (15)  NOT NULL default '',
`domain_name` VARCHAR (255)  NOT NULL default '',
`domain_subdomain` VARCHAR (32)  NOT NULL default '',
`domain_port` INTEGER default '0',
PRIMARY KEY (`domain_id`)
);

CREATE TABLE `{PREFIX}database` (
`database_id` INTEGER auto_increment ,
`database_type` VARCHAR (15)  NOT NULL default '',
`database_server` VARCHAR (64)  NOT NULL default '',
`database_port` INTEGER NOT NULL default '0',
`database_name` VARCHAR (32)  NOT NULL default '',
`database_user` VARCHAR (32)  NOT NULL default '',
`database_pass` VARCHAR (32)  NOT NULL default '',
`database_prefix` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`database_id`)
);
