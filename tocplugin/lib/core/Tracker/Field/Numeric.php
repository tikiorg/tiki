<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_Numeric extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable
{
	public static function getTypes()
	{
		return array(
			'n' => array(
				'name' => tr('Numeric Field'),
				'description' => tr('Provides a one-line field for numeric input only. Prepended or appended values may be alphanumeric.'),
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
						'legacy_index' => 0,
					),
					'size' => array(
						'name' => tr('Display Size'),
						'description' => tr('Visible size of the field, in characters. Does not affect the numeric length.'),
						'filter' => 'int',
						'legacy_index' => 1,
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text to be displayed in front of the numeric value.'),
						'filter' => 'text',
						'legacy_index' => 2,
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text to be displayed after the numeric value.'),
						'filter' => 'text',
						'legacy_index' => 3,
					),
					'decimals' => array(
						'name' => tr('Decimal Places'),
						'description' => tr('Number of decimal places to round to.'),
						'filter' => 'int',
						'legacy_index' => 4,
					),
					'dec_point' => array(
						'name' => tr('Decimal separator when displaying data'),
						'description' => tr('Single character. Use "c" for comma, "d" for dot or "s" for space. The valid decimal separator when inserting numbers may depend on the site language and web browser. See the documentation for more details.'),
						'filter' => 'text',
						'default' => '.',
						'legacy_index' => 5,
					),
					'thousands' => array(
						'name' => tr('Thousands separator when displaying data'),
						'description' => tr('Single character: use "c" for comma, "d" for dot or "s" for space.  When inserting data, no thousands separator is needed.'),
						'filter' => 'text',
						'default' => ',',
						'legacy_index' => 6,
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

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$item = $this->getValue();
		$baseKey = $this->getBaseKey();

		$out = array(
			$baseKey => $typeFactory->numeric($item),
		);
		return $out;
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

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$schema->addNew($permName, 'default')
			->setLabel($this->getConfiguration('name'))
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			})
			;

		$prepend = $this->getOption('prepend');
		$append = $this->getOption('append');
		$schema->addNew($permName, 'formatted')
			->setLabel($this->getConfiguration('name'))
			->addIncompatibility($permName, 'default')
			->setRenderTransform(function ($value) use ($prepend, $append) {
				return $prepend . $value . $append;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName, $prepend, $append) {
				$value = substr($value, strlen($prepend), -strlen($append));
				$info['fields'][$permName] = $value;
			})
			;

		return $schema;
	}
}

