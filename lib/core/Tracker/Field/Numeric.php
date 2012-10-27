<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for numeric and currency field
 * 
 * Letter key: ~n~
 *
 */
class Tracker_Field_Numeric extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'n' => array(
				'name' => tr('Numeric Field'),
				'description' => tr('Provides a one-line field for numeric input only. Prepend or append values may be alphanumeric.'),
				'help' => 'Numeric Tracker Field',
				'prefs' => array('trackerfield_numeric'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Displays the next field on the same line.'),
						'deprecated' => false,
						'filter' => 'int',
						'default' => 1,
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'size' => array(
						'name' => tr('Display Size'),
						'description' => tr('Visible size of the field in characters. Does not affect the numeric length.'),
						'filter' => 'int',
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text to be displayed prior to the numeric value.'),
						'filter' => 'text',
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text to be displayed after the numeric value.'),
						'filter' => 'text',
					),
					'decimals' => array(
						'name' => tr('Decimal Places'),
						'description' => tr('Amount of decimals to preserve before rounding.'),
						'filter' => 'int',
					),
					'dec_point' => array(
						'name' => tr('Decimal separator when displaying data'),
						'description' => tr('Single character. Use c for comma, d for dot or s for space. The valid decimal separator when inserting numbers may depend on site language and web browser. See documentation for more details.'),
						'filter' => 'text',
						'default' => '.',
					),
					'thousands' => array(
						'name' => tr('Thousand separator when displaying data'),
						'description' => tr('Single character,  Use c for comma, d for dot or s for space.  When inserting data no thousands separator is needed.'),
						'filter' => 'text',
						'default' => ',',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderInnerOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/numeric.tpl', $context);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/numeric.tpl', $context);
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
}

