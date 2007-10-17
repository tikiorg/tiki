<?php
require_once('tiki-setup.php');

/*
 * This script convert templates with the old preferences $truc to $pref.truc by getting the currently existing prefs keys.
 * Use with caution !
 * Copy it to the root of you're tiki, and run it with:
 * php convert_templates_prefs-1.10.php
 */

/* customize this to the directory you want to convert */
$dirtoscan='templates';

/*****************/

$src=array();
$dst=array();

foreach(array_keys($prefs) as $k => $v) {
    $src[$k]='$'.$v;
    $dst[$k]='$prefs.'.$v;
}

$elems=scandir('templates/');

foreach($elems as $filename) {
    if (preg_match('/.tpl$/', $filename)) {
	echo "$filename...\n";
	$content=file_get_contents($dirtoscan.'/'.$filename);
	$content=str_replace($src, $dst, $content);
	file_put_contents($dirtoscan.'/'.$filename, $content);
    }
}

?>