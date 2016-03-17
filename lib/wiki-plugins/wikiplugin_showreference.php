<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_showreference_info()
{
	return array(
		'name' => tra('Add Bibliography'),
		'documentation' => 'PluginShowReference',
		'description' => tra('Add bibliography listing in the footer of a wiki page'),
		'format' => 'html',
		'iconname' => 'list',
		'introduced' => 10,
		'prefs' => array('wikiplugin_showreference','feature_references'),
		'params' => array(
			'title' => array(
				'required' => false,
				'name' => tra('Title'),
				'description' => tr('Title to be displayed in the bibliography listing. Default is %0Bibliography%1.',
					'<code>', '</code>'),
				'since' => '10.0',
				'default' => 'Bibliography',
				'filter' => 'text',
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show bibliography title. Title is shown by default.'),
				'since' => '10.0',
				'filter' => 'word',
				'options' => array(
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'yes'),
					array('text' => tra('No'), 'value' => 'no'),
				),
				'default' => '',
			),
			'hlevel' => array(
				'required' => false,
				'name' => tra('Header Tag'),
				'description' => tr('The HTML header tag level of the title. Default: %01%1', '<code>', '</code>'),
				'since' => '10.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('0'), 'value' => '0'),
					array('text' => tra('1'), 'value' => '1'),
					array('text' => tra('2'), 'value' => '2'),
					array('text' => tra('3'), 'value' => '3'),
					array('text' => tra('4'), 'value' => '4'),
					array('text' => tra('5'), 'value' => '5'),
					array('text' => tra('6'), 'value' => '6'),
					array('text' => tra('7'), 'value' => '7'),
					array('text' => tra('8'), 'value' => '8'),
				),
				'filter' => 'digits',
				'default' => '',
			),
		),
	);
}

function wikiplugin_showreference($data, $params)
{

	global $prefs;

	$params['title'] = trim($params['title']);
	$params['showtitle'] = trim($params['showtitle']);
	$params['hlevel'] = trim($params['hlevel']);

	$title = 'Bibliography';
	if (isset($params['title']) && $params['title'] != '') {
		$title = $params['title'];
	}

	if (isset($params['showtitle'])) {
		$showtitle = $params['showtitle'];
	}
	if ($showtitle == 'yes' || $showtitle == '') {
		$showtitle = 1;
	} else {
		$showtitle = 0;
	}

	$hlevel_start = '<h1>';
	$hlevel_end = '</h1>';

	if (isset($params['hlevel']) && $params['hlevel']!='') {
		if ($params['hlevel']!='0') {
			$hlevel_start = '<h' . $params['hlevel'] . '>';
			$hlevel_end = '</h' . $params['hlevel'] . '>';
		} else {
			$hlevel_start = '';
			$hlevel_end = '';
		}
	} else {
		$hlevel_start = '<h1>';
		$hlevel_end = '</h1>';
	}

	if ($prefs['wikiplugin_showreference'] == 'y') {

		$page_id = $GLOBALS['info']['page_id'];

		$tags = array(
				'~biblio_code~'=>'biblio_code',
				'~author~'=>'author',
				'~title~'=>'title',
				'~year~'=>'year',
				'~part~'=>'part',
				'~uri~'=>'uri',
				'~code~'=>'code',
				'~publisher~'=>'publisher',
				'~location~'=>'location'
		);

		$htm = '';

		$referenceslib = TikiLib::lib('references');
		$references = $referenceslib->list_assoc_references($page_id);

		$referencesData = array();
		$is_global = 1;
		if (isset($GLOBALS['referencesData']) && is_array($GLOBALS['referencesData'])) {
			$referencesData = $GLOBALS['referencesData'];
			$is_global = 1;
		} else {
			foreach ($references['data'] as $data) {
				array_push($referencesData, $data['biblio_code']);
			}
			$is_global = 0;
		}

		if (is_array($referencesData)) {

			$referencesData = array_unique($referencesData);

			$htm .= '<div class="references">';

			if ($showtitle) {
				$htm .= $hlevel_start . $title . $hlevel_end;
			}

			$htm .= '<hr>';

			$htm .= '<ul style="list-style: none outside none;">';

			if (count($referencesData)) {
				$values = $referenceslib->get_reference_from_code_and_page($referencesData, $page_id);
			} else {
				$values = array();
			}

			if ($is_global) {
				$excluded = array();
				foreach ($references['data'] as $key=>$value) {
					if (!array_key_exists($key, $values['data'])) {
						$excluded[$key] = $references['data'][$key]['biblio_code'];
					}
				}
				foreach ($excluded as $ex) {
					array_push($referencesData, $ex);
				}
			}

			foreach ($referencesData as $index=>$ref) {

				$ref_no = $index + 1;

				$text = '';
				$cssClass = '';
				if (array_key_exists($ref, $values['data'])) {

					if ($values['data'][$ref]['style'] != '') {
						$cssClass = $values['data'][$ref]['style'];
					}

					$text = parseTemplate($tags, $ref, $values['data']);
				} else {
					if (array_key_exists($ref, $excluded)) {
						$text = parseTemplate($tags, $ref, $references['data']);
					}
				}
				$anchor = "<a name='" . $ref . "'>&nbsp;</a>";
				if (strlen($text)) {
					$htm .= "<li class='" . $cssClass . "'>" . $anchor . $ref_no . ". " . $text . '</li>';
				} else {
					$htm .= "<li class='" . $cssClass . "' style='font-style:italic'>" . $anchor .
											$ref_no . '. missing bibliography definition' .
											'</li>';
				}
			}

			$htm .= '</ul>';

			$htm .= '<hr>';

			$htm .= '</div>';

		}

		return $htm;
	}
}

function parseTemplate($tags, $ref, $values)
{

	$text = $values[$ref]['template'];
	if ($text == '') {
		$text = '~title~, ~part~, ~author~, ~location~, ~year~, ~publisher~, ~code~';
	}

	if ($text != '') {
		foreach ($tags as $tag=>$val) {
			if ($values[$ref][$val] == '') {
				$pos = strpos($text, $tag);
				$len = strlen($tag);
				$prevWhiteSpace = $text[$pos - 1];

				if ($prevWhiteSpace != ' ' && $pos) {
					$text = str_replace($text[$pos - 1], '', $text);
				}

				$pos = strpos($text, $tag);
				$len = strlen($tag);
				$postWhiteSpace = $text[$pos + $len];

				if ($postWhiteSpace != ' ' && $pos) {
					$text = str_replace($text[$pos + $len], '', $text);
				}

				$text = str_replace($tag, $values[$ref][$val], $text);
			} else {
				$text = str_replace($tag, $values[$ref][$val], $text);
			}
		}
	}
	return $text;
}
