<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 *
 * SURVEY plugin. Display a survey.
 *
 * Syntax:
 *
 * {SURVEY(id=xxx, lang=xxx)}{SURVEY}
 *
 */
function wikiplugin_survey_help() {
	return tra("survey").":~np~{SURVEY(id=xxx, lang=xxx)}{SURVEY}~/np~";
}

function wikiplugin_survey_info() {
	return array(
		'name' => tra('Survey'),
		'documentation' => 'PluginSurvey',		
		'description' => tra('Displays a survey'),
		'prefs' => array( 'feature_surveys', 'wikiplugin_survey' ),
		'body' => '',
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Id'),
				'description' => tra('Id'),
			),
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Wiki Page to redirect the user after his vote'),
			),
			'lang' => array(
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('Language'),
			),
		),
	);
}

function wikiplugin_survey($data, $params) {
	global $tiki_p_take_survey;
	if ( $tiki_p_take_survey != 'y' ) return '';

	$result = '';
	if ( !isset($params['id']) ) return '';

	// Check if user has taken this survey
	global $tiki_p_admin, $tikilib, $user;
	if ( $tiki_p_admin != 'y' ) {
		if ( $tikilib->user_has_voted($user, 'survey'.$params['id']) ) {
			include_once('lib/wiki-plugins/wikiplugin_remarksbox.php');
			return wikiplugin_remarksbox(
				'You cannot take this survey twice',
				array('type' => 'comment')
			);
		}
	}

	global $srvlib; include_once ('lib/surveys/surveylib.php');
	$questions = $srvlib->list_survey_questions($params['id'], 0, -1, 'position_asc', '');

	$error_msg = '';
	if ( isset($_REQUEST['surveyId']) && $_REQUEST['surveyId'] == $params['id'] ) {
		if ( isset($_REQUEST['vote']) ) {
			$srvlib->add_survey_hit($_REQUEST['surveyId']);
		}
		if ( isset($_REQUEST['ans']) ) {
			$srvlib->register_answers($params['id'], $questions['data'], $_REQUEST, $error_msg);
			if ( $error_msg == '' ) {
				$location = isset($params['page']) ?
					'tiki-index.php?page='.urlencode($params['page'])
					: 'tiki-list_surveys.php';
				header('Location: '.$location);
			}
		}
	}

	$survey_info = $srvlib->get_survey($params['id']);

	global $smarty;
	$smarty->assign('surveyId', $params['id']);
	$smarty->assign('survey_info', $survey_info);
	$smarty->assign('questions', $questions['data']);
	$smarty->assign('error_msg', $error_msg);
	$smarty->assign('show_name', 'n');

	include_once('lib/smarty_tiki/function.query.php');
	$smarty->assign('form_action', smarty_function_query(array('_type' => 'absolute_path'), $smarty));

	if ( !empty($params['lang']) ) {
		$result .= $smarty->fetchLang($params['lang'], 'tiki-take_survey.tpl');
	} else {
		$result .= $smarty->fetch('tiki-take_survey.tpl');
	}
	$result = '~np~'.$result.'~/np~';

	return $result;
}
