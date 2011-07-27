<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		return $this->renderTemplate('trackerinput/textarea.tpl', $context);
	}

	/*
	function handleSave($value, $oldValue)
	{
		$length = $this->getOption(3);

		if ($length) {
			$f_len = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
			$f_substr = function_exists('mb_substr') ? 'mb_substr' : 'substr';

			if ($f_len($value) > $length) {
				$value = $f_substr($value, 0, $length);
			}
		}

		return array(
			'value' => $value,
		);
	}
	*/
}

