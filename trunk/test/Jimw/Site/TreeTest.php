<?php
echo 'Test Jimw_Site_Tree';
include ('Jimw/Site/Tree.php');
$tree = new Jimw_Site_Tree();
echo 'Test GetChildrens';
$root = $tree->getChildrens(0);
foreach ($root as $item) {
	Zend_Debug::dump($item);
}