<?php
require_once('tiki-setup.php');

/*
 * This script search in php files every $smarty->assign("truc" where truc is a preference key
 * Copy it to the root of you're tiki, and run it with:
 * php search_smarty_prefs-1.10.php
 */

/* customize this to the directory you want to scan */
$dirtoscan='.';

/*****************/
$kprefs=array();
foreach($prefs as $k => $v) $kprefs[]=preg_quote($k);

// split the array in smaller array, because preg_match have some limitations
$src=array();
for ($i=0; $i<count($kprefs); $i+=500) {
    $src[]='/\$smarty->assign\([\'"]('.implode('|', array_slice($kprefs, $i, 500)).')[\'"]/';
}
$elems=scandir($dirtoscan);

foreach($elems as $filename) {
    if (preg_match('/.php$/', $filename)) {
	$content_src=file_get_contents($dirtoscan.'/'.$filename);
	$gmatchs=array();
	foreach($src as $regexp) {
	    $matchs=array();
	    $count=preg_match_all($regexp, $content_src, $matchs);
	    if ($count === false) die("error: $regexp");
	    if ($count) {
		foreach($matchs[1] as $v) $gmatchs[]=$v;
	    }
	}
	if (count($gmatchs)) {
	    //var_dump($matchs);
	    echo "$filename: ".implode(', ', $gmatchs)."\n";
	}
    }
}

?>