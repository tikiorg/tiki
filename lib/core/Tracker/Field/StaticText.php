<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Static text
 * 
 * Letter key: ~S~
 *
 */
class Tracker_Field_StaticText extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'S' => array(
				'name' => tr('Static Text'),
				'description' => tr('Displays the field description as static text that can be used to display notes or additional instructions.'),
				'readonly' => true,
				'params' => array(
					'wikiparse' => array(
						'name' => tr('Wiki Parse'),
						'description' => tr('Indicates if the description should be parsed as wiki syntax for formatting.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Handle line breaks as new lines only'),
							1 => tr('Wiki Parse'),
						),
					),
					'max' => array(
						'name' => tr('Maximum Length (List)'),
						'description' => tr('Maximum amount of characters to be displayed in list mode'),
						'filter' => 'int',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $tikilib;
		
		$value = $this->getConfiguration('description');

		if ($this->getOption(0) == 1) {
			$value = $tikilib->parse_data($value);
		}
		
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/statictext.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		return array(
			'value' => false,
		);
	}

	function import($value)
	{
		return '';
	}

	function export($value)
	{
		return '';
	}
}

