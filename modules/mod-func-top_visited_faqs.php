<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @return array
 */
function module_top_visited_faqs_info()
{
	return [
		'name' => tra('Top Visited FAQs'),
		'description' => tra('Display the specified number of FAQs with links to them, from the most visited one to the least.'),
		'prefs' => ['feature_faqs'],
		'params' => [],
		'common_params' => ['nonums', 'rows']
	];
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_top_visited_faqs($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');

	$faqlib = TikiLib::lib('faq');
	$ranking = $faqlib->list_faqs(0, $mod_reference["rows"], 'hits_desc', '');

	$smarty->assign('modTopVisitedFaqs', $ranking["data"]);
}
