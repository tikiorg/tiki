<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function module_last_validated_faq_questions_info() {
	return array(
		'name' => tra('Newest Validated FAQ Questions'),
		'description' => tra('Displays the specified number of validated questions FAQs from newest to oldest.'),
		'prefs' => array("feature_faqs"),
		'params' => array(
			'faqId' => array(
				'name' => tra('Faq identifier'),
				'description' => tra('If set to a faq identifier, restricts the chosen questions to those in the identified faq.') . " " . tra('Example value: 13.') . " " . tra('Not set by default.'),
				'filter' => 'int'
			),
			'truncate' => array(
				'name' => tra('Number of characters to display'),
				'description' => tra('Number of characters to display'),
				'filter' => 'int'
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_validated_faq_questions( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	global $faqlib; include_once('lib/faqs/faqlib.php');
	$def = array('faqId'=>0, 'truncate'=>20);
	$module_params = array_merge($def, $module_params);
	$ranking = $faqlib->list_faq_questions($module_params['faqId'], 0, $mod_reference['rows'], 'created_desc', '');
	$smarty->assign_by_ref('modLastValidatedFaqQuestions', $ranking['data']);
	$smarty->assign_by_ref('trunc', $module_params['truncate']);
	
}
