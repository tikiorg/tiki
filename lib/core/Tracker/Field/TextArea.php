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
						'legacy_index' => 0,
					),
					'width' => array(
						'name' => tr('Width'),
						'description' => tr('Size of the text area in characters.'),
						'filter' => 'int',
						'legacy_index' => 1,
					),
					'height' => array(
						'name' => tr('Height'),
						'description' => tr('Size of the text area in lines.'),
						'filter' => 'int',
						'legacy_index' => 2,
					),
					'max' => array(
						'name' => tr('Character Limit'),
						'description' => tr('Maximum amount of characters to be stored.'),
						'filter' => 'int',
						'legacy_index' => 3,
					),
					'listmax' => array(
						'name' => tr('Display Limit (List)'),
						'description' => tr('Maximum amount of characters to be displayed in list mode before the value gets truncated.'),
						'filter' => 'int',
						'legacy_index' => 4,
					),
					'wordmax' => array(
						'name' => tr('Word Count'),
						'description' => tr('Limit the length of the text in words.'),
						'filter' => 'int',
						'legacy_index' => 5,
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
						'legacy_index' => 6,
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
						'legacy_index' => 7,
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
						'legacy_index' => 8,
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
		static $firstTime = true;

		$cols = $this->getOption('width');
		$rows = $this->getOption('height');

		$data = array(
			'toolbar' => $this->getOption('toolbars') ? 'y' : 'n',
			'cols' => ($cols >= 1) ? $cols : 80,
			'rows' => ($rows >= 1) ? $rows : 6,
			'keyup' => '',
		);

		if ($this->getOption('wordwrap')) {
			$data['keyup'] = "wordCount({$this->getOption('wordwrap')}, this, 'cpt_{$this->getConfiguration('fieldId')}', '" . tr('Word Limit Exceeded') . "')";
		} elseif ($this->getOption('max')) {
			$data['keyup'] = "charCount({$this->getOption('max')}, this, 'cpt_{$this->getConfiguration('fieldId')}', '" . tr('Character Limit Exceeded') . "')";
		}
		$data['element_id'] = 'area_' . uniqid();
		if ($firstTime && $this->getOption('wysiwyg') === 'y') {	// wysiwyg
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
		if ($this->getOption('wysiwyg') === 'y') {
			$parseOptions['is_html'] = true;
		}
		return TikiLib::lib('tiki')->parse_data($text, $parseOptions);
	}

	protected function getIndexableType()
	{
		return 'wikitext';
	}
}

