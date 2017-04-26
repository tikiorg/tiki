<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

if ($prefs['ids_enabled'] == 'n') {
	$access->display_error('', tra("Tiki IDS is not enabled"), '403', false);
}

$access->check_permission('tiki_p_admin');

if (isset($_POST['new_rule'])) {

	$id = $_POST['rule_id'];
	$rule = new IDS_Rule($id);

	$rule->setRegex($_POST['rule_regex']);
	$rule->setDescription($_POST['rule_description']);
	$rule->setTags($_POST['rule_tags']);
	$rule->setImpact($_POST['rule_impact']);

	//Check if a custom rule with the same ID already existes
	$conflictRule = IDS_Rule::getRule($_POST['rule_id']);
	if (empty($conflictRule)) {
		$rule->save();
		$cookietab = 1;
	} else {
		Feedback::error(tra('A custom rule with the same ID already exists.'), 'session');
		$ruleinfo = [
			'id' => $rule->getId(),
			'regex' => $rule->getRegex(),
			'description' => $rule->getDescription(),
			'tags' => implode(', ', $rule->getTags()),
			'impact' => $rule->getImpact(),
			'error' => 1,
		];
	}

} else if (isset($_POST['editrule']) and isset($_POST['rule_id'])) {

	$rule = IDS_Rule::getRule($_POST['rule_id']);

	$rule->setRegex($_POST['rule_regex']);
	$rule->setDescription($_POST['rule_description']);
	$rule->setTags($_POST['rule_tags']);
	$rule->setImpact($_POST['rule_impact']);
	$rule->save();

	$cookietab = '1';

} else if (isset($_REQUEST['rule']) and $_REQUEST['rule']) {

	$rule = IDS_Rule::getRule($_REQUEST['rule']);

	if (!empty($rule)) {
		$ruleinfo = [
			'id' => $rule->getId(),
			'regex' => $rule->getRegex(),
			'description' => $rule->getDescription(),
			'tags' => implode(', ', $rule->getTags()),
			'impact' => $rule->getImpact(),
		];
	} else {
		$ruleinfo = [
			'id' => '',
			'regex' => '',
			'description' => '',
			'tags' => '',
			'impact' => '',
		];
	}

	$cookietab = 2;
} else {
	$_REQUEST['rule'] = 0;
}

if (isset($_REQUEST['add'])) {
	$cookietab = '2';
}

$idsRules = [];

foreach (IDS_Rule::getAllRules() as $rule) {
	$idsRules[] = [
		'id' => $rule->getId(),
		'regex' => $rule->getRegex(),
		'description' => $rule->getDescription(),
		'tags' => implode(', ', $rule->getTags()),
		'impact' => $rule->getImpact(),
	];
}

$smarty->assign('ids_rules', $idsRules);
$smarty->assign('ruleinfo', $ruleinfo);
$smarty->assign('ruleId', $_REQUEST['rule']);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_ids.tpl');
$smarty->display('tiki.tpl');
