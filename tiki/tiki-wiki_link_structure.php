<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
 die;
}
$tikilib->wiki_link_structure();
?>