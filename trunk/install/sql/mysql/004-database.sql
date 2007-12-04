ALTER TABLE `site_tree` ADD `tree_lft` INT NOT NULL AFTER `tree_parentid` ,
ADD `tree_rgt` INT NOT NULL AFTER `tree_lft` ;