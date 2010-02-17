<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This rather simple set of PHP lines fill in for a missing Tiki
// feature. It exports links between pages in Tiki in a format that
// can be fed to TGBrowser to get those nice browsable graphs.
require_once('tiki-setup.php');

// Check if user is admin. If he is not, just die.  Note that I wrote
// this since I do not know too much about Tiki permission system, so
// I would kindly ask a wizard to enlarge this. (filmil)
if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', 
			tra("This feature is for admins only").
			": show_raw_links");
	$smarty->display("error.tpl");
	die;
}

// Otherwise do the following:
// 1. Find all Wiki pages
// 2. For each Wiki page P, find all pages that it points to
// 3. Print first the name of the page P, then one after the other,
//    all pages that it points to.

header("Content-Type: text/plain; charset=utf-8");
$query = "select `pageName` from `tiki_pages`";
$result = $tikilib->query($query, array());
while ($res = $result->fetchRow()) {
	$resnew = str_replace(" ", '+', $res); // Must do this since
					       // TGBrowser will get confused
	$put = "";
	$query2 = "select `toPage`, `fromPage` from `tiki_links` where `fromPage`='".
		$res["pageName"]."'";
	$result2 = $tikilib->query($query2,array());
	$put = $put.$resnew["pageName"]." ";
	while($res2 = $result2->fetchRow()) {
		$res2 = str_replace(" ", '+', $res2);
		$put = $put.$res2["toPage"]." ";
	}
	echo $put."\n";
}
