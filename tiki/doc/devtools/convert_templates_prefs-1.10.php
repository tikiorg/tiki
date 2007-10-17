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

$elems=scandir($dirtoscan);

foreach($elems as $filename) {
    if (preg_match('/.tpl$/', $filename)) {
	echo "$filename... ";
	$content_src=file_get_contents($dirtoscan.'/'.$filename);
	$content_dst=str_replace($src, $dst, $content_src);
	if ($content_dst != $content_src) {
	    file_put_contents($dirtoscan.'/'.$filename, $content_dst);
	    echo " modified\n";
	} else {
	    echo " no\n";
	}
    }
}

?>