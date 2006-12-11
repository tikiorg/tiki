<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_faqs.php,v 1.11 2006-12-11 22:36:15 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["faqcomprefs"])) {
	check_ticket('admin-inc-faqs');
	simple_set_value('faq_comments_per_page');
	simple_set_value('faq_comments_default_ordering');
	simple_set_toggle('feature_faq_comments');
}
ask_ticket('admin-inc-faqs');
?>
