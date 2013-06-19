<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for TextArea
 *
 * Letter key: ~a~
 *
 */
class Tracker_Field_TextArea extends Tracker_Field_Text
{
	public static function getTypes()
	{
		return array(
			'a' => array(
				'name' => tr('Text Area'),
				'description' => tr('Multi-line text input.'),
				'help' => 'Textarea Tracker Field',
				'prefs' => array('trackerfield_textarea'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'toolbars' => array(
						'name' => tr('Toolbars'),
						'description' => tr('Enable the toolbars as syntax helpers.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Disable'),
							1 => tr('Enable'),
						),
					),
					'width' => array(
						'name' => tr('Width'),
						'description' => tr('Size of the text area in characters.'),
						'filter' => 'int',
					),
					'height' => array(
						'name' => tr('Height'),
						'description' => tr('Size of the text area in lines.'),
						'filter' => 'int',
					),
					'max' => array(
						'name' => tr('Character Limit'),
						'description' => tr('Maximum amount of characters to be stored.'),
						'filter' => 'int',
					),
					'listmax' => array(
						'name' => tr('Display Limit (List)'),
						'description' => tr('Maximum amount of characters to be displayed in list mode before the value gets truncated.'),
						'filter' => 'int',
					),
					'wordmax' => array(
						'name' => tr('Word Count'),
						'description' => tr('Limit the length of the text in words.'),
						'filter' => 'int',
					),
					'distinct' => array(
						'name' => tr('Distinct Values'),
						'description' => tr('All values in the field must be different.'),
						'filter' => 'alpha',
						'default' => 'n',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
					),
					'wysiwyg' => array(
						'name' => tr('Use WYSIWYG'),
						'description' => tr('Use a rich text editor instead of a plain text box.'),
						'default' => 'n',
						'filter' => 'alpha',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
					),
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Display the field name and input on the same row.'),
						'deprecated' => false,
						'filter' => 'int',
						'default' => 1,
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$data = $this->processMultilingual($requestData, $ins_id);

		global $user;
		if (isset($requestData[$ins_id])) {
			$newvalue = TikiLib::lib('parser')->process_save_plugins(
				$data['value'],
				array(
					'type' => 'trackeritem',
					'itemId' => $this->getItemId(),
					'user' => $user,
				)
			);
			if ($newvalue !== $data['value']) {
				$data['value'] = $newvalue;
				$data['pvalue'] = $this->attemptParse($newvalue);
			}
		}

		return $data;
	}

	function renderInput($context = array())
	{
		static $firstTime = true;

		$cols = $this->getOption(1);
		$rows = $this->getOption(2);

		$data = array(
			'toolbar' => $this->getOption(0) ? 'y' : 'n',
			'cols' => ($cols >= 1) ? $cols : 80,
			'rows' => ($rows >= 1) ? $rows : 6,
			'keyup' => '',
		);

		if ($this->getOption(5)) {
			$data['keyup'] = "wordCount({$this->getOption(5)}, this, 'cpt_{$this->getConfiguration('fieldId')}', '" . tr('Word Limit Exceeded') . "')";
		} elseif ($this->getOption(3)) {
			$data['keyup'] = "charCount({$this->getOption(3)}, this, 'cpt_{$this->getConfiguration('fieldId')}', '" . tr('Character Limit Exceeded') . "')";
		}
		$data['element_id'] = 'area_' . uniqid();
		if ($firstTime && $this->getOption(7) === 'y') {	// wysiwyg
			$is_html = '<input type="hidden" id="allowhtml" value="1" />';
			$firstTime = false;
		} else {
			$is_html = '';
		}
		return $this->renderTemplate('trackerinput/textarea.tpl', $context, $data) . $is_html;
	}

	function renderInnerOutput($context = array())
	{
		$output = parent::renderInnerOutput($context);

		if (!empty($context['list_mode']) && $context['list_mode'] === 'y' && $this->getOption('listmax')) {
			TikiLib::lib('smarty')->loadPlugin('smarty_modifier_truncate');
			return smarty_modifier_truncate(strip_tags($output), $this->getOption('listmax'));
		} else {
			return $output;
		}
	}


	protected function attemptParse($text)
	{
		$parseOptions = array();
		if ($this->getOption(7) === 'y') {
			$parseOptions['is_html'] = true;
		}
		return TikiLib::lib('tiki')->parse_data($text, $parseOptions);
	}

	protected function getIndexableType()
	{
		return 'wikitext';
	}
}

