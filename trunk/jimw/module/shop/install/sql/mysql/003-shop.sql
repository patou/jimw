ALTER TABLE `{PREFIX}shopsong` ADD `shopsong_musicref` VARCHAR(30) NOT NULL AFTER `shopsong_musicprice`, ADD `shopsong_scoreref` VARCHAR(30) NOT NULL AFTER `shopsong_scoreprice`, ADD `shopsong_lyrics` TEXT NOT NULL AFTER `shopsong_year`;
ALTER TABLE `{PREFIX}shopalbum` ADD `shopalbum_musicref` VARCHAR(30) NOT NULL, ADD `shopalbum_scoreref` VARCHAR(30) NOT NULL;
ALTER TABLE `{PREFIX}shopsongorder` ADD `shopsongorder_content` TEXT NOT NULL AFTER `shopsongorder_date` ;
ALTER TABLE `{PREFIX}shopsongorder` DROP `shopsongorder_albummusiclist`, DROP `shopsongorder_albumscorelist`, DROP `shopsongorder_musiclist`, DROP `shopsongorder_scorelist`;
