<?php
error_reporting(E_ALL);
require 'XML/Tree.php';
ob_implicit_flush(true);
// Build a xml file
//  /*  <-- (un)comment this line for the 2º example
$tree = new XML_Tree;
$root = $tree->addRoot('MyFriends');
$foo  = $root->addChild('name', 'Foo');
$bar  = $root->addChild('name', 'Bar', array('age' => 21));
$tree->dump();
print_r($root);
$root->dump();
exit;
// /**/

// Map a xml file to an object tree
$tree = new XML_Tree('../package.xml');
$root = $tree->getTreeFromFile();
print_r($root);
$root->dump();
?>