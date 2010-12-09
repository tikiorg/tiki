<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('feature_mobile');

require ("lib/hawhaw/hawhaw.inc");
$TikiPage = new HAW_deck("Tiki", HAW_ALIGN_CENTER);

$text1 = new HAW_text("Attention!", HAW_TEXTFORMAT_BOLD);
$text1->set_br(2);
$TikiPage->add_text($text1);

$text2 = new HAW_text("Usage of tiki-wap.php is deprecated! The HAWHAW-enabled Tiki is available at:");
$text2->set_br(2);
$TikiPage->add_text($text2);

$newLink = new HAW_link("tiki-mobile.php", "tiki-mobile.php");
$newLink->set_br(2);
$TikiPage->add_link($newLink);

$text3 = new HAW_text(
	"Reason: This URL is not restricted to WAP only. It can be used by PDA's, voice browser and other mobile devices too.");
$TikiPage->add_text($text3);

$TikiPage->create_page();
