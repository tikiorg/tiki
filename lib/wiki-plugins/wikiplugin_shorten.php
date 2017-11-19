<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_shorten_info()
{
	return [
		'name' => tra('Shorten'),
		'documentation' => 'Shorten',
		'description' => tra('Show/hide a portion of text'),
		'prefs' => ['wikiplugin_shorten', 'wikiplugin_button'],
		'body' => tra('Code to be displayed'),
		'iconname' => 'shorten',
		'introduced' => 17,
		'filter' => 'rawhtml_unsafe',
		'format' => 'html',
		'tags' => [ 'basic' ],
		'params' => [
			'length' => [
				'required' => true,
				'name' => tra('Length'),
				'description' => tra('Char length of always visible portion of text.'),
				'since' => '17',
				'filter' => 'digits',
			],
			'moreText' => [
				'required' => false,
				'name' => tra('More text'),
				'description' => tra('A label indicating the text is expandable.'),
				'since' => '17',
				'filter' => 'text',
			],
			'lessText' => [
				'required' => false,
				'name' => tra('Less text'),
				'description' => tra('A label for collapse text action.'),
				'since' => '17',
				'filter' => 'text',
			],
			'show_speed' => [
				'required' => false,
				'name' => tra('Show Speed'),
				'filter' => 'alnum',
				'description' => tr('Speed of animation in milliseconds when showing content (%0200%1 is fast and
					%0600%1 is slow. %01000%1 equals 1 second).', '<code>', '</code>'),
				'default' => 0,
				'since' => '17',
				'accepted' => tr(
					'Integer greater than 0 and less than or equal to 1000, or %0 or %1',
					'<code>fast</code>',
					'<code>slow</code>'
				),
				'advanced' => true,
			],
			'hide_speed' => [
				'required' => false,
				'name' => tra('Hide Speed'),
				'filter' => 'alnum',
				'description' => tr('Speed of animation in milliseconds when hiding content (%0200%1 is fast and
					%0600%1 is slow. %01000%1 equals 1 second).', '<code>', '</code>'),
				'default' => 0,
				'since' => '17',
				'accepted' => tr(
					'Integer greater than 0 and less than or equal to 1000, or %0 or %1',
					'<code>fast</code>',
					'<code>slow</code>'
				),
				'advanced' => true,
			],

		],
	];
}

function wikiplugin_shorten($data, $params)
{
	static $shorten_count;
	$headerlib = TikiLib::lib('header');
	$parserlib = TikiLib::lib('parser');

	extract([
		'length' => 20,
		'moreText' => '[+]',
		'lessText' => '[-]',
		'show_speed' => 0,
		'hide_speed' => 0,
	]);

	if (isset($params['length'])) {
		$length = (int) sprintf('%d', $params['length']);
		$length = max(1, $length);
	}

	if (isset($params['show_speed'])) {
		$show_speed = str_replace(['slow', 'fast'], ['600', '200'], $params['show_speed']);
		$show_speed = sprintf(' data-show-speed="%d"', $show_speed);
	}

	if (isset($params['hide_speed'])) {
		$hide_speed = str_replace(['slow', 'fast'], ['600', '200'], $params['hide_speed']);
		$hide_speed = sprintf(' data-hide-speed="%d"', $hide_speed);
	}

	$match = null;
	if (preg_match('/^\s*.{' . $length . '}[^\s]*/', $data, $match)) {
		if (! $shorten_count) {
			$headerlib->add_css(
				".wikiplugin-shorten .content { display: none; }"
				. ".wikiplugin-shorten .btn_less, .wikiplugin-shorten .btn_more { cursor: pointer; margin-left: 0.5em; }"
				. ".wikiplugin-shorten .btn_less { display: none; }"
			);

			$headerlib->add_jq_onready(
				'$(".wikiplugin-shorten").each(function(){'
				. 'var $this = $(this);'
				. 'var $sample = $this.find("> .sample");'
				. 'var $content = $this.find("> .content");'
				. 'var show_speed = $this.data("show-speed") || 0;'
				. 'var hide_speed = $this.data("hide-speed") || 0;'

				. 'var $btn_more = $sample.find(".btn_more:first");'
				. 'var $btn_less = $content.find(".btn_less:last");'

				. '$btn_more.click(function(){'
				. '$sample.hide();'
				. '$btn_more.hide();'
				. '$btn_less.show();'
				. '$content.show(show_speed);'
				. 'return false;'
				. '});'

				. '$btn_less.click(function(){'
				. '$sample.show();'
				. '$btn_less.hide();'
				. '$btn_more.show();'
				. '$content.hide(hide_speed);'
				. 'return false;'
				. '});'
				. '});'
			);
		}

		$shorten_count += 1;

		if (isset($params['moreText'])) {
			$moreText = strip_tags($params['moreText']);
		}

		if (isset($params['lessText'])) {
			$lessText = strip_tags($params['lessText']);
		}

		$html = '<div class="wikiplugin-shorten"' . $show_speed . $hide_speed . '>';
		$html .= '<div class="sample"  data-btn-more="' . $moreText . '">%s</div>';
		$html .= '<div class="content" data-btn-less="' . $lessText . '">%s</div>';
		$html .= '</div>';

		$index = strlen($match[0]);

		$sample = $parserlib->parse_data_plugin(substr($data, 0, $index));
		$sample .= '<a href="#" class="btn_more btn btn-default">' . $moreText . '</a>';

		$content = $parserlib->parse_data_plugin($data) . '<a href="#" class="btn_less btn btn-default">' . $lessText . '</a>';

		$out = sprintf($html, $sample, $content);
		return $out;
	} else {		// short enough already

		return $parserlib->parse_data_plugin($data);
	}
}
