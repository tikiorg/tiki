<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mobile.php,v 1.8 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ("lib/hawhaw/hawtikilib.php");

$TikiPage = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
$TikiPage->enable_session();

if ($_REQUEST['content'] == "about") {
	// show info about hawiki
	$title = new HAW_text(hawtra("Welcome at Hawiki"), HAW_TEXTFORMAT_BOLD);

	$title->set_br(2);
	$TikiPage->add_text($title);

	$text1 = new HAW_text(
		hawtra("This TikiWiki site is prepared for access from a lot of mobile devices, e.g. WAP phones, PDA's, i-mode devices and much more."));
	$text1->set_br(2);
	$TikiPage->add_text($text1);

	$text2 = new HAW_text(
		hawtra("You can browse this site on your mobile device by directing your device's browser towards the following URL here on this site:"));
	$TikiPage->add_text($text2);

	$home = new HAW_link(hawtra("tiki-mobile.php"), $_SERVER['PHP_SELF']);
	$TikiPage->add_link($home);
} else {
	// HAWIKI main menu
	$title = new HAW_text(HAWIKI_TITLE, HAW_TEXTFORMAT_BOLD | HAW_TEXTFORMAT_BIG);

	$TikiPage->add_text($title);

	$linkset = new HAW_linkset();

	//$forum = new HAW_link("Forum","forum.php");
	//$linkset->add_link($forum);
	$wiki = new HAW_link(hawtra("Wiki"), "tiki-index.php?mode=mobile");

	if ($feature_wiki == 'y') {
		$linkset->add_link($wiki);
	}

	$blogs = new HAW_link(hawtra("Blogs"), "tiki-list_blogs.php?mode=mobile");

	if ($feature_blogs == 'y') {
		$linkset->add_link($blogs);
	}

	$articles = new HAW_link(hawtra("Articles"), "tiki-list_articles.php?mode=mobile");

	if ($feature_articles == 'y') {
		$linkset->add_link($articles);
	}

	//$about = new HAW_link(hawtra("About"),"tiki-index.php?page=AboutHawiki&mode=mobile");
	$about = new HAW_link(hawtra("About"), $_SERVER['PHP_SELF'] . "?content=about");
	$linkset->add_link($about);

	$TikiPage->add_linkset($linkset);
}

$TikiPage->create_page();

?>
