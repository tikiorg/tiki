<?php

include_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');

$str = $wikilib->wiki_get_link_structure($_REQUEST['page'], 0);

echo sprintf('<graph node="%s">',$str['name']);

foreach ($str['pages'] as $page) {
    echo sprintf('<link name="%s"></link>',$page['name']);
}

echo '</graph>';

?>

