CREATE TABLE IF NOT EXISTS `{PREFIX}shopalbum` (
`shopalbum_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tree_id` INT NOT NULL ,
`shopalbum_title` VARCHAR( 128 ) NOT NULL ,
`shopalbum_artist` VARCHAR( 64 ) NOT NULL ,
`shopalbum_editor` VARCHAR( 64 ) NOT NULL ,
`shopalbum_year` CHAR( 4 ) NOT NULL ,
`shopalbum_musicfile` VARCHAR( 128 ) NOT NULL ,
`shopalbum_scorefile` VARCHAR( 128 ) NOT NULL ,
`shopalbum_musicprice` DECIMAL( 5, 2 ) NOT NULL ,
`shopalbum_scoreprice` DECIMAL( 5, 2 ) NOT NULL ,
INDEX ( `tree_id` )
);

CREATE TABLE IF NOT EXISTS `{PREFIX}shopsong` (
`shopsong_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`shopalbum_id` INT NOT NULL ,
`shopsong_number` TINYINT NOT NULL ,
`shopsong_title` VARCHAR( 128 ) NOT NULL ,
`shopsong_artist` VARCHAR( 64 ) NOT NULL ,
`shopsong_editor` VARCHAR( 64 ) NOT NULL ,
`shopsong_year` CHAR( 4 ) NOT NULL ,
`shopsong_musicfile` VARCHAR( 128 ) NOT NULL ,
`shopsong_musicpreview` VARCHAR( 128 ) NOT NULL ,
`shopsong_musicprice` DECIMAL( 5, 2 ) NOT NULL ,
`shopsong_scorefile` VARCHAR( 128 ) NOT NULL ,
`shopsong_scorepreview` VARCHAR( 128 ) NOT NULL ,
`shopsong_scoreprice` DECIMAL( 5, 2 ) NOT NULL ,
INDEX ( `shopalbum_id` )
);