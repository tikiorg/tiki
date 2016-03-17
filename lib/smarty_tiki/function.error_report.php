<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_error_report($params, $smarty)
{
	$errorreportlib = TikiLib::lib('errorreport');
	$errors = $errorreportlib->get_errors();

	$pre = '<div id="error_report">';
	$post = '</div>';

	$repeat = false;
	$legacy = $smarty->getTemplateVars('display_msg');
	$type = $smarty->getTemplateVars('display_msgtype');
	$type = empty($type) ? 'note' : $type;
	$titles = [
		'confirm' => tra('Success'),
		'feedback' => tra('Success'),
		'error' => tra('Error'),
		'errors' => tra('Errors'),
		'warning' => tra('Warning'),
		'note' => tra('Notice')
	];
	if ($legacy) {
		// Handle reporting ofthe display_msg smarty variable
		$smarty->loadPlugin('smarty_block_remarksbox');
		$post .= smarty_block_remarksbox(array(
			'type' => $type,
			'title' => $titles[$type],
		), $legacy, $smarty, $repeat);
	}

	TikiLib::lib('header')->add_js(
		'
	$(document).ajaxComplete(function (e, jqxhr) {
		var error = jqxhr.getResponseHeader("X-Tiki-Error");
		if (error) {
			if ($("ul", "#error_report").length === 0) {
				$("#error_report").append($(error)[0].childNodes);
			} else {
				$("ul", "#error_report").append($(error).find("li"));
			}
		}
		$("#error_report .clear").on("click", function () {
			$("#error_report").empty();
			return false;
		});
	});
	'
	);

	if (count($errors)) {
		$smarty->loadPlugin('smarty_block_remarksbox');

		$repeat = false;
		return $pre . smarty_block_remarksbox(
			array(
				'type' => 'errors',
				'title' => tra('Error(s)'),
			),
			'<ul><li>' . implode('</li><li>', $errors) . '</li></ul>',
			$smarty,
			$repeat
		) . $post;
	} else {
		return $pre . $post;
	}
}

