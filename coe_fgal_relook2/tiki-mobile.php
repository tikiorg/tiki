<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ("lib/hawhaw/hawtikilib.php");
$access->check_feature('feature_mobile');
$TikiPage = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
HAWTIKI_deck_init($TikiPage);
if (isset($_REQUEST['content']) && $_REQUEST['content'] == "about") {
	// show info about hawiki
	$title = new HAW_text(hawtra("Welcome at Hawiki"), HAW_TEXTFORMAT_BOLD);
	$title->set_br(2);
	$TikiPage->add_text($title);
	$text1 = new HAW_text(hawtra("This Tiki site is prepared for access from a lot of mobile devices, e.g. WAP phones, PDA's, i-mode devices and much more."));
	$text1->set_br(2);
	$TikiPage->add_text($text1);
	$text2 = new HAW_text(hawtra("You can browse this site on your mobile device by directing your device's browser towards the following URL here on this site:"));
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
