<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/ebay/wiki-plugins/wikiplugin_ebay.php,v 1.2 2004-09-13 11:22:26 damosoft Exp $

// Wiki plugin to display ebay search results file
// damian aka damosoft 13 Sep 2004

function wikiplugin_ebay_help() {
        return tra("Displays a button linking to ebay search results").":<br />~np~{EBAY(search=>keywords)/}~/np~";
}

function wikiplugin_ebay($data, $params) {
	
	extract ($params);

	$asetup = '<form action="http://search.ebay.co.uk/search/search.dll" method="get">';
	$asetup .='<input type="hidden" name="sokeywordredirect">';
	$asetup .='<input type="hidden" name="satitle" value="'.$search.'">';
	$asetup .= '<input type="submit" value="Find It" alt="Go!">';
	$asetup .= '</form>';

	return $asetup;
}

?>
