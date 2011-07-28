<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for numeric and currency field
 * 
 * Letter key: 
 *  numeric: ~n~
 *  currency: ~b~
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
				'params' => array(
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Displays the next field on the same line.'),
						'deprecated' => true,
						'filter' => 'int',
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
						'name' => tr('Decimal separator'),
						'description' => tr('Single character, conventions depend on the language of the site.'),
						'filter' => 'text',
						'default' => '.',
					),
					'thousands' => array(
						'name' => tr('Thousand separator'),
						'description' => tr('Single character, conventions depend on the language of the site.'),
						'filter' => 'text',
						'default' => ',',
					),
				),
			),
			'b' => array(
				'name' => tr('Currency Field'),
				'description' => tr('Provides a one-line field for numeric input only. Prepend or append values may be alphanumeric.'),
				'params' => array(
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Displays the next field on the same line.'),
						'deprecated' => true,
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'size' => array(
						'name' => tr('Display Size'),
						'description' => tr('Visible size of the field in characters. Does not affect the numeric length.'),
						'filter' => 'int',
						'default' => 7,
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text to be displayed prior to the numeric value.'),
						'filter' => 'text',
						'default' => '$',
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text to be displayed after the numeric value.'),
						'filter' => 'text',
						'default' => ' USD',
					),
					'decimals' => array(
						'name' => tr('Decimal Places'),
						'description' => tr('Amount of decimals to preserve before rounding.'),
						'filter' => 'int',
						'default' => 2,
					),
					'dec_point' => array(
						'name' => tr('Decimal separator'),
						'description' => tr('Single character, conventions depend on the language of the site.'),
						'filter' => 'text',
						'default' => '.',
					),
					'thousands' => array(
						'name' => tr('Thousand separator'),
						'description' => tr('Single character, conventions depend on the language of the site.'),
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

	function import($value)
	{
		return $value;
	}

	function export($value)
	{
		return $value;
	}
}

