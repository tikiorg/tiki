<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/wiki/wikilib.php');


if($feature_wiki != 'y') {
 die;
}
$wikilib->wiki_link_structure();
?>