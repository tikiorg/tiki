<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Text
 * 
 * Letter key: ~t~
 *
 */
class Tracker_Field_Text extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			't' => array(
				'name' => tr('Text Field'),
				'description' => tr('Single-line text input.'),
				'params' => array(
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Display the next field on the same row.'),
						'deprecated' => true,
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'size' => array(
						'name' => tr('Display Size'),
						'description' => tr('Visible size of the field in characters.'),
						'filter' => 'int',
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text to prepend when displaying the value.'),
						'filter' => 'text',
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text to prepend when displaying the value.'),
						'filter' => 'text',
					),
					'max' => array(
						'name' => tra('Maximum Length'),
						'description' => tra('Maximum amount of characters to store.'),
						'filter' => 'int',
					),
					'autocomplete' => array(
						'name' => tra('Autocomplete'),
						'description' => tra('Enable autocompletion while typing in the field.'),
						'filter' => 'alpha',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$data = $this->processMultilingual($requestData, $this->getInsertId());

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/text.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		$pre = '';
		$post = '';

		if ($this->getConfiguration('type') == 't') {
			if ($this->getOption(2)) {
				$pre = '<span class="formunit">' . $this->getOption(2) . '</span>';
			}

			if ($this->getOption(3)) {
				$post = '<span class="formunit">' . $this->getOption(3) . '</span>';
			}
		}

		return $pre . parent::renderInnerOutput($context) . $post;
	}

	protected function processMultilingual($requestData, $id_string) {
		global $prefs;
		$language = $prefs['language'];
		$multilingual = $this->getConfiguration('isMultilingual') == 'y';

		if (!isset($requestData[$id_string])) {
			$requestData[$id_string] = $this->getValue('', $multilingual ? $language : '');
		}
		
		if (is_array($requestData[$id_string])) {
			$thisVal = $requestData[$id_string][$language];
		} else {
			$thisVal = $requestData[$id_string];
		}

		$data = array(
			'value' => $thisVal,
			'pvalue' => $thisVal,
			'lingualvalue' => array(),
			'lingualpvalue' => array(),
		);

		if ($this->getConfiguration('type') != 't') {	// textareas are parsed, text not
			$data['pvalue'] = TikiLib::lib('tiki')->parse_data(htmlspecialchars($thisVal));
		}
		// Trim ending \n added by parsing
		$data['pvalue'] = trim($data['pvalue'], "\n");

		if ($this->getConfiguration("isMultilingual") == 'y') {
			if (! is_array($requestData[$id_string])) {
				$out = array();
				foreach($prefs['available_languages'] as $num => $tmplang) {	// TODO add a limit on number of langs - 40+ makes this blow up
					if (!isset($out[$tmplang])) {	// Case convert normal -> multilingual
						$out[$tmplang] = $this->getValue($data['value'], $tmplang);
					}
				}

				$requestData[$id_string] = $out;
			}

			foreach($prefs['available_languages'] as $num => $tmplang) {	// TODO add a limit on number of langs - 40+ makes this blow up
				if (!isset($requestData[$id_string][$tmplang])) {	// Case convert normal -> multilingual
					$requestData[$id_string][$tmplang] = $this->getValue($data['value'], $tmplang);
				}

				$data['lingualvalue'][$num]['lang'] = $tmplang;
				$data['lingualvalue'][$num]['value'] = $requestData[$id_string][$tmplang];
				$data['lingualpvalue'][$num]['lang'] = $tmplang;
				if ($this->getConfiguration('type') != 't') {	// textareas are parsed, text not
					$data['lingualpvalue'][$num]['value'] = TikiLib::lib('tiki')->parse_data(htmlspecialchars($requestData[$id_string][$tmplang]));
				} else {
					$data['lingualpvalue'][$num]['value'] = $requestData[$id_string][$tmplang];
				}

				if ($prefs['language'] == $tmplang) {
					$data['value'] = $data['lingualvalue'][$num]['value'];
					$data['pvalue'] = $data['lingualpvalue'][$num]['value'];
				}
			}
		}

		return $data;
	}
}

