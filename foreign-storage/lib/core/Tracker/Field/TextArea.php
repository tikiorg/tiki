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
	function getFieldData(array $requestData = array())
	{
		$data = $this->processMultilingual($requestData, $this->getInsertId());

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/textarea.tpl', $context);
	}

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
}

