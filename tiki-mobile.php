<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mobile.php,v 1.14 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ("lib/hawhaw/hawtikilib.php");

if ($prefs['feature_mobile'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_mobile");

	$smarty->display("error.tpl");
	die;
}

$TikiPage = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
HAWTIKI_deck_init($TikiPage);

if (isset($_REQUEST['content']) && $_REQUEST['content'] == "about") {
	// show info about hawiki
	$title = new HAW_text(hawtra("Welcome at Hawiki"), HAW_TEXTFORMAT_BOLD);

	$title->set_br(2);
	$TikiPage->add_text($title);

	$text1 = new HAW_text(
		hawtra("This Tikiwiki site is prepared for access from a lot of mobile devices, e.g. WAP phones, PDA's, i-mode devices and much more."));
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

	$wiki = new HAW_link(hawtra("Wiki"), "tiki-index.php?mode=mobile");

	if ($prefs['feature_wiki'] == 'y') {
		$linkset->add_link($wiki);
	}

	$blogs = new HAW_link(hawtra("Blogs"), "tiki-list_blogs.php?mode=mobile");

	if ($prefs['feature_blogs'] == 'y') {
		$linkset->add_link($blogs);
	}

	$articles = new HAW_link(hawtra("Articles"), "tiki-list_articles.php?mode=mobile");

	if ($prefs['feature_articles'] == 'y') {
		$linkset->add_link($articles);
	}

	$forums = new HAW_link(hawtra("Forums"), "tiki-forums.php?mode=mobile");

	if ($prefs['feature_forums'] == 'y') {
		$linkset->add_link($forums);
	}

	$about = new HAW_link(hawtra("About"), $_SERVER['PHP_SELF'] . "?content=about");
	$linkset->add_link($about);

	$TikiPage->add_linkset($linkset);
}

$TikiPage->create_page();

?>
