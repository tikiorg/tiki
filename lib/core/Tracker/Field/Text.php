<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_Text extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			't' => array(
				'name' => tr('Text Field'),
				'description' => tr('Single-line text input.'),
				'help' => 'Text Tracker Field',
				'prefs' => array('trackerfield_text'),
				'tags' => array('basic'),
				'default' => 'y',
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
					'exact' => array(
						'name' => tr('Index exact value'),
						'description' => tr('In addition to indexing the content of the field, also index it as an identifier in tracker_field_{perm name}_exact. This option is not available for multilingual fields. Mostly for identifiers like product codes or ISBN numbers.'),
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

	function renderOutput($context = array())
	{
		if (isset($context['history']) && $context['history'] == 'y' && is_array($this->getConfiguration('value'))) {
			return $this->renderTemplate('trackeroutput/text_history.tpl');
		} else {
			return parent::renderOutput($context);
		}
	}

	protected function processMultilingual($requestData, $id_string) 
	{
		global $prefs;
		$language = $prefs['language'];
		$multilingual = $this->getConfiguration('isMultilingual') == 'y';

		if (!isset($requestData[$id_string])) {
			$value = $this->getValue();
			if ($multilingual) {
				$requestData[$id_string] = @json_decode($value, true);

				if ($requestData[$id_string] === false) {
					$requestData[$id_string] = $value;
				}
			} else {
				$requestData[$id_string] = $value;
			}
		}
		
		$data['raw'] = $requestData[$id_string];

		if (is_array($data['raw'])) {
			$thisVal = $data['raw'][$language];
		} else {
			$thisVal = $data['raw'];
		}

		$data = array(
			'value' => $data['raw'],
			'pvalue' => trim($this->attemptParse($thisVal), "\n"),
			'lingualvalue' => array(),
			'lingualpvalue' => array(),
		);

		if ($multilingual) {
			foreach ($prefs['available_languages'] as $num => $lang) { // TODO add a limit on number of langs - 40+ makes this blow up
				if (!isset($data['raw'][$lang])) {
					$data['raw'][$lang] = $thisVal;
				}

				$data['lingualvalue'][$num]['lang'] = $lang;
				$data['lingualvalue'][$num]['value'] = $requestData[$id_string][$lang];
				$data['lingualpvalue'][$num]['lang'] = $lang;
				$data['lingualpvalue'][$num]['value'] = $this->attemptParse($requestData[$id_string][$lang]);
			}
		}

		unset($data['raw']);

		return $data;
	}

	protected function attemptParse($text)
	{
		return $text;
	}

	function handleSave($value, $oldValue)
	{
		if (is_array($value)) {
			return array(
				'value' => json_encode(array_map(array($this, 'filterValue'), $value)),
			);
		} else {
			return array(
				'value' => $this->filterValue($value),
			);
		}
	}

	function filterValue($value)
	{
		$length = $this->getOption('max');

		if ($length) {
			$f_len = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
			$f_substr = function_exists('mb_substr') ? 'mb_substr' : 'substr';

			if ($f_len($value) > $length) {
				return $f_substr($value, 0, $length);
			}
		}

		return $value;
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		$value = $this->getValue();
		$fieldType = $this->getIndexableType();

		if ($this->getConfiguration('isMultilingual') == 'y') {
			$decoded = json_decode($value, true);
			$value = implode("\n", $decoded);

			$data = array($baseKey => $typeFactory->$fieldType($value));
			foreach ($decoded as $lang => $content) {
				$data[$baseKey . '_' . $lang] = $typeFactory->$fieldType($content);
			}

			return $data;
		} else {
			$data = array(
				$baseKey => $typeFactory->$fieldType($value),
			);

			if ($this->getOption('exact') == 'y') {
				$data[$baseKey . '_exact'] = $typeFactory->identifier($value);
			}

			return $data;
		}
	}

	function getProvidedFields($baseKey)
	{
		global $prefs;

		$data = array($baseKey);

		if ($this->getConfiguration('isMultilingual') == 'y') {
			foreach ($prefs['available_languages'] as $lang) {
				$data[] = $baseKey . '_' . $lang;
			}
		} elseif ($this->getOption('exact') == 'y') {
			$data[] = $baseKey . '_exact';
		}

		return $data;
	}

	function getGlobalFields($baseKey)
	{
		global $prefs;

		$data = array($baseKey => true);

		if ($this->getConfiguration('isMultilingual') == 'y') {
			foreach ($prefs['available_languages'] as $lang) {
				$data[$baseKey . '_' . $lang] = true;
			}
		}

		return $data;
	}

	protected function getIndexableType()
	{
		return 'sortable';
	}
}

