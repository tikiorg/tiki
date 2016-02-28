<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_exercise_info()
{
	return array(
		'name' => tra('Exercise'),
		'documentation' => tra('PluginExercise'),
		'description' => tra('Create an exercise for a user to complete and grade'),
		'prefs' => array('wikiplugin_exercise'),
		'filter' => 'text',
		'format' => 'html',
		'iconname' => 'education',
		'introduced' => 9,
		'tags' => array('basic'),
		'params' => array(
			'answer' => array(
				'required' => false,
				'name' => tr('Answer'),
				'description' => tr('Used inline to specify the right answer to the question and propose an input field.'),
				'since' => '9.0',
				'filter' => 'text',
			),
			'incorrect' => array(
				'required' => false,
				'name' => tr('Incorrect'),
				'description' => tr('Alternative answers to provide'),
				'since' => '9.0',
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_exercise($data, $params)
{
	static $nextId = 1;
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_escape');

	$params = new JitFilter($params);
	$answer = $params->answer->text();

	if (isset(TikiLib::lib('parser')->option['indexing']) && TikiLib::lib('parser')->option['indexing']) {
		return "{$params->answer->text()} {$params->incorrect->text()}";
	}

	if ($answer) {
		$escapedAnswer = smarty_modifier_escape($answer);
		$escapedId = smarty_modifier_escape('exercise-' . $nextId++);

		if ($incorrect = $params->incorrect->text()) {
			$exercises = wikiplugin_exercise_parse_argument($incorrect);
			wikiplugin_exercise_process_group($exercises, '#' . $escapedId);
		}

		return <<<HTML
<span id="$escapedId" class="exercise-input" data-answer="$escapedAnswer">___________</span>
HTML;
	} else {
		$exercises = wikiplugin_exercise_parse_data($data);
		wikiplugin_exercise_process_group($exercises);
		return wikiplugin_exercise_finalize();
	}
}

function wikiplugin_exercise_parse_data($data)
{
	$exercises = array();
	$key = -1;

	foreach (explode("\n", $data) as $line) {
		$line = trim($line);

		if (empty($line)) {
			continue;
		}

		if (substr($line, 0, 3) === '---') {
			$key = count($exercises);
			$exercises[] = array();
		} elseif ($key !== -1) {
			$parts = array_map('trim', explode(':', $line, 2));
			$exercises[$key][] = array('option' => array_shift($parts), 'justification' => array_shift($parts));
		}
	}

	return $exercises;
}

function wikiplugin_exercise_parse_argument($data)
{
	$out = array();
	$answers = explode('+', $data);
	foreach ($answers as $possibility) {
		if (preg_match('/^\s*([^\(]+)(:\s*\(\s*(.*)\s*\))?\s*/', $possibility, $parts)) {
			$out[] = array('option' => $parts[1], 'justification' => isset($parts[2]) ? $parts[2] : false);
		}
	}

	return array($out);
}

function wikiplugin_exercise_process_group($exercises, $scope = '.exercise-input')
{
	$headerlib = TikiLib::lib('header');

	$js = <<<JS
$.exerciseGroup = function (exercises, scope) {
	var shuffle = function(o){
	for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	};
	$.each(exercises, function (k, options) {
		$(scope + ':not(.done)').filter(':first').each(function (k, container) {
			var answer = $(container).data('answer'), input;

			$(container).addClass('done').empty();
			if (options.length > 0) {
				input = $('<select><option/></select>');
				options.push({option: answer, justification: false});
				options = shuffle(options);

				$.each(options, function (k, o) {
					input.append($('<option/>')
						.val(o.option)
						.text(o.option)
						.data('justification', o.justification ? o.justification : ''));
				});
			} else {
				input = $('<input type="text"/>');
				input.attr('size', answer.length);
			}

			input.appendTo(container);
		});
	});
};
JS;
	$headerlib->add_js($js);

	$exercises = json_encode($exercises);
	$headerlib->add_js("$.exerciseGroup($exercises, '$scope');");
}

function wikiplugin_exercise_finalize()
{
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_icon');

	$checkYourScore = smarty_modifier_escape(tr('Check your score'));
	$yourScoreIs = tr('You scored %0 out of %1', '~SCORE~', '~TOTAL~');
	$checkIcon = smarty_function_icon(array('_id' => 'tick', 'title' => tr('Good!')), $smarty);
	$crossIcon = smarty_function_icon(array('_id' => 'cross', 'title' => tr('Oops!')), $smarty);

	$js = <<<JS
$.exerciseFinalize = function (random) {
	$('.exercise-form').filter(':first').removeClass('exercise-form').each(function (k, form) {
		var label = $('p', form).hide().text(), elements = $('.exercise-input.done:not(.complete)').addClass('complete');
		$(form).submit(function () {
			var score = 0, total = 0;

			elements.find('.mark').remove();

			elements.each(function (k, container) {
				var correct, input, image;
				total += 1;
				correct = $(container).data('answer');
				input = $(':input', container).val();

				image = $('<span class="mark"/>')
					.appendTo(container);

				if (correct.toString() === input) {
					score += 1;
					image.append('$checkIcon');
				} else {
					image.append('$crossIcon');

					var just = $('option:selected', container).data('justification');
					if (just) {
						image.find('img').attr('title', just);
					}
				}
			});

			$('p', form).text(label.replace('~SCORE~', score).replace('~TOTAL~', total)).show();
			return false;
		});
	});
};
JS;
	$headerlib = TikiLib::lib('header');
	$headerlib->add_js($js);

	static $id = 0;
	++$id;
	$headerlib->add_js("$.exerciseFinalize($id);");

	return <<<HTML
<form class="exercise-form" method="get" action="#">
	<p>$yourScoreIs</p>
	<input type="submit" class="btn btn-default btn-sm" value="$checkYourScore"/>
</form>
HTML;
}

