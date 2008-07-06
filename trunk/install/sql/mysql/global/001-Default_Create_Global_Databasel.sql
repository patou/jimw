CREATE TABLE IF NOT EXISTS `{PREFIX}domain` (
`domain_id` INTEGER AUTO_INCREMENT  PRIMARY KEY,
`database_id` INTEGER DEFAULT '0',
`site_id` INT NOT NULL DEFAULT '1',
`domain_protocol` VARCHAR (15)  NOT NULL DEFAULT '',
`domain_name` VARCHAR (255)  NOT NULL DEFAULT '',
`domain_subdomain` VARCHAR (32)  NOT NULL DEFAULT '',
`domain_port` INTEGER DEFAULT '0',
`domain_path` VARCHAR( 255 ) NOT NULL ,
`domain_status` TINYINT( 4 ) NOT NULL
);

CREATE TABLE IF NOT EXISTS `{PREFIX}database` (
`database_id` INTEGER AUTO_INCREMENT  PRIMARY KEY,
`database_type` VARCHAR (15)  NOT NULL DEFAULT '',
`database_server` VARCHAR (64)  NOT NULL DEFAULT '',
`database_port` INTEGER NOT NULL DEFAULT '0',
`database_name` VARCHAR (255)  NOT NULL DEFAULT '',
`database_user` VARCHAR (32)  NOT NULL DEFAULT '',
`database_pass` VARCHAR (32)  NOT NULL DEFAULT '',
`database_prefix` VARCHAR (32)  NOT NULL DEFAULT ''
);
