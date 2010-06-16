CREATE TABLE  `{PREFIX}newsletter` (
	`newsletter_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`tree_alias` VARCHAR( 255 ) NOT NULL ,
	`newsletter_title` VARCHAR( 255 ) NOT NULL ,
	`newsletter_message` TEXT NOT NULL ,
	`newsletter_send` INT NOT NULL ,
	`newsletter_senddate` DATE NULL ,
	`newsletter_draft` INT NOT NULL
);

CREATE TABLE  `{PREFIX}newsletteruser` (
	`newsletteruser_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`tree_alias` VARCHAR( 255 ) NOT NULL ,
	`newsletteruser_email` VARCHAR( 255 ) NOT NULL ,
	`newsletteruser_lastsend` INT NOT NULL ,
	`newsletteruser_valide` INT( 0 ) NOT NULL
);