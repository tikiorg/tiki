<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_feedback($params, $smarty)
{
	$result = Feedback::get();

	TikiLib::lib('header')->add_js(
		'
	$(document).ajaxComplete(function (e, jqxhr) {
		var error = jqxhr.getResponseHeader("X-Tiki-Feedback");
		if (error) {
			if ($("ul", "#tikifeedback").length === 0) {
				$("#tikifeedback").html(error);
			} else {
				$("ul", "#tikifeedback").append($(error).find("li"));
				$("ul", "#tikifeedback").removeClass("list-unstyled");
			}
			$("html, body").animate({
				scrollTop: $("div#tikifeedback").offset().top
			}, 500);
		}
		$("#tikifeedback .clear").on("click", function () {
			$("#tikifeedback").empty();
			return false;
		});
	});
	'
	);

	if (is_array($result)) {
		$smarty->assign('tikifeedback', $result);
	}
	$ret = $smarty->fetch('feedback/default.tpl');
	return $ret;
}

