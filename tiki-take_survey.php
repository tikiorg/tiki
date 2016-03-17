<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'surveys';
require_once ('tiki-setup.php');
include_once ('lib/surveys/surveylib.php');
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$access->check_feature('feature_surveys');

if (!isset($_REQUEST["surveyId"])) {
	$smarty->assign('msg', tra("No survey indicated"));
	$smarty->display("error.tpl");
	die;
}
$access->check_permission('take_survey', 'Take Survey', 'survey', $_REQUEST['surveyId']);

$smarty->assign('surveyId', $_REQUEST["surveyId"]);
$survey_info = $srvlib->get_survey($_REQUEST["surveyId"]);
$smarty->assign('survey_info', $survey_info);

// Check if user has taken this survey
if ($tiki_p_admin != 'y') {
	if ($tikilib->user_has_voted($user, 'survey' . $_REQUEST["surveyId"])) {
		$smarty->assign('msg', tra("You cannot take this survey twice"));
		$smarty->display("error.tpl");
		die;
	}
}
$questions = $srvlib->list_survey_questions($_REQUEST["surveyId"], 0, -1, 'position_asc', '');
$smarty->assign('pagination', false);
foreach($questions['data'] as $question) {
	if ($question['type'] === 'h' && !empty($question['explode']) && $question['explode'][0] === 'y') {
		$smarty->assign('pagination', true);
		$headerlib->add_css('.questionblock, .submit {display:none;}')
			->add_jq_onready('
(function($) {
	var surveyPage, surveyPageCount = 0, surveyHeight = 0, h = 0, beenToLastPage = false;
	if (typeof surveyKeepSameHeight === "undefined") {
		surveyKeepSameHeight = false;
	}
	$(".questionblock").each(function () {
		h += $(this).outerHeight(true);
		if ($(this).hasClass("page" + (surveyPageCount + 1))) {
			surveyPageCount++;
			if (h > surveyHeight) {
				surveyHeight = h;
			}
			h = 0;
		}
	});
	if (surveyKeepSameHeight) {
		if (h > surveyHeight) {
			surveyHeight = h;
		}
		$(".surveyquestions").height(surveyHeight + $(".submit").outerHeight(true));
	}
	var showPage = function (page) {
		if (page < 1) {
			page = 0;
			$(".btn-prev").attr("disabled", true);
			$(".btn-next").attr("disabled", false);
		} else if (page >= surveyPageCount) {
			page = surveyPageCount;
			$(".btn-next").attr("disabled", true);
			$(".btn-prev").attr("disabled", false);
		} else {
			$(".btn-next").attr("disabled", false);
			$(".btn-prev").attr("disabled", false);
		}
		if (page != surveyPage) {
			surveyPage = page;
			var sTop = $(".surveyquestions").offset().top - 10;
			if ($(window).scrollTop() > sTop) {
				$(\'html, body\').animate({
					scrollTop: sTop
				}, 1000);
			}
			$(".questionblock:visible").slideUp("fast");
			$(".page" + surveyPage).slideDown("fast");
			location.hash = "page" + surveyPage;
			$(".pageNum").text(surveyPage + 1);
			$(".pageCount").text(surveyPageCount + 1);
			if (surveyPage === surveyPageCount) {
				beenToLastPage = true;
				$(".submit").show("fast");
			} else if (!beenToLastPage) {
				$(".submit").hide("fast");
			}
		}
	};
	$(".btn-prev").click(function () {
		showPage(surveyPage - 1);
		return false;
	});
	$(".btn-next").click(function () {
		showPage(surveyPage + 1);
		return false;
	});
	$(window).on("hashchange load", function () {
		var goPage = location.hash.match(/page(\d+)/);
		if (goPage) {
			showPage(Number(goPage[1]));
		} else {
			showPage(0);
		}
	});
})(jQuery)
			');
		break;
	}
}
$smarty->assign_by_ref('questions', $questions["data"]);
$error_msg = '';
if (isset($_REQUEST["ans"])) {
	check_ticket('take-survey');
	$srvlib->register_answers($_REQUEST['surveyId'], $questions['data'], $_REQUEST, $error_msg);
	if (empty($error_msg)) {
		if (!empty($_REQUEST["vote"])) {
			$srvlib->add_survey_hit($_REQUEST["surveyId"]);
		}
		header('Location: tiki-list_surveys.php');
		die;
	}
}

$showToolBars = false;
if($prefs['poll_surveys_textarea_hidetoolbar'] != 'y')
	$showToolBars = true;
$smarty->assign('showToolBars', $showToolBars);

include_once ('tiki-section_options.php');
ask_ticket('take-survey');
// Display the template
$smarty->assign('error_msg', $error_msg);
$smarty->assign('mid', 'tiki-take_survey.tpl');
$smarty->display("tiki.tpl");
