<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_faqs.php,v 1.5 2004-03-27 21:23:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

if (isset($_REQUEST["faqcomprefs"])) {
	check_ticket('admin-inc-faqs');
	if (isset($_REQUEST["faq_comments_per_page"])) {
		$tikilib->set_preference("faq_comments_per_page", $_REQUEST["faq_comments_per_page"]);

		$smarty->assign('faq_comments_per_page', $_REQUEST["faq_comments_per_page"]);
	}

	if (isset($_REQUEST["faq_comments_default_ordering"])) {
		$tikilib->set_preference("faq_comments_default_ordering", $_REQUEST["faq_comments_default_ordering"]);

		$smarty->assign('faq_comments_default_ordering', $_REQUEST["faq_comments_default_ordering"]);
	}

	if (isset($_REQUEST["feature_faq_comments"]) && $_REQUEST["feature_faq_comments"] == "on") {
		$tikilib->set_preference("feature_faq_comments", 'y');

		$smarty->assign("feature_faq_comments", 'y');
	} else {
		$tikilib->set_preference("feature_faq_comments", 'n');

		$smarty->assign("feature_faq_comments", 'n');
	}
}
ask_ticket('admin-inc-faqs');
?>
