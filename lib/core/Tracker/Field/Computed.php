<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Computed
 *
 * Letter key: ~C~
 *
 */
class Tracker_Field_Computed extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'C' => array(
				'name' => tr('Computed Field'),
				'description' => tr('Provides a computed value based on numeric field values. Consider using webservices or JavaScript to perform the task instead of using this type.'),
				'help' => 'Computed Tracker Field',
				'prefs' => array('trackerfield_computed'),
				'tags' => array('advanced'),
				'default' => 'n',
				'warning' => tra('This feature is still in place for backward compatibility. While it has no known flaws, it could be used as a vector for a malicious attack. A webservice field or custom JavaScript is recommended instead of this field.'),
				'params' => array(
					'formula' => array(
						'name' => tr('Formula'),
						'description' => tr('The formula to be computed supporting various operators (+ - * / and parenthesis), references to other field made using the field id preceeded by #.'),
						'example' => '#3*(#4+5)',
						'filter' => 'text',
						'legacy_index' => 0,
						'profile_reference' => array(__CLASS__, 'profileReference'),
					),
					'decimals' => array(
						'name' => tr('Decimal Places'),
						'description' => tr('Number of decimal places to round to.'),
						'filter' => 'int',
						'legacy_index' => 1,
					),
					'dec_point' => array(
						'name' => tr('Decimal separator when displaying data'),
						'description' => tr('Single character. Use "c" for comma, "d" for dot or "s" for space. The valid decimal separator when inserting numbers may depend on the site language and web browser. See the documentation for more details.'),
						'filter' => 'text',
						'default' => '.',
						'legacy_index' => 2,
					),
					'thousands' => array(
						'name' => tr('Thousand separator when displaying data'),
						'description' => tr('Single character: use "c" for comma, "d" for dot or "s" for space.  When inserting data, no thousands separator is needed.'),
						'filter' => 'text',
						'default' => ',',
						'legacy_index' => 3,
					),					
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$data = array();

		if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];
		} else if ($this->getItemId()) {
			$fields = $this->getTrackerDefinition()->getFields();
			$values = $this->getItemData();
			$option = $this->getOption('formula');

			if ($option) {
				$calc = preg_replace('/#([0-9]+)/', '$values[\1]', $option);
				// FIXME: kill eval()
				eval('$computed = ' . $calc . ';');
				$value = $computed;

				$trklib = TikiLib::lib('trk');

				$infoComputed = $trklib->get_computed_info(
					$this->getOption('formula'),
					$this->getTrackerDefinition()->getConfiguration('trackerId'),
					$fields
				);

				if ($infoComputed) {
					$data = array_merge($data, $infoComputed);
				}
			}
		}

		$data['value'] = $value;

		return $data;
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/computed.tpl', $context);
	}

	function renderInput($context = array())
	{
		return $this->renderOutput($context);
	}

	function handleSave($value, $oldValue)
	{
		return array(
			'value' => false,
		);
	}

	public static function computeFields($args)
	{
		$trklib = TikiLib::lib('trk');
		$definition = Tracker_Definition::get($args['trackerId']);

		foreach ($definition->getFields() as $field) {
			$fieldId = $field['fieldId'];

			if ($field['type'] == 'C') {
				$calc = preg_replace('/#([0-9]+)/', '$args[\'values\'][\1]', $field['options_array'][0]);
				eval('$value = '.$calc.';');
				$args['values'][$fieldId] = $value;
				$trklib->modify_field($args['itemId'], $fieldId, $value);
			}
		}
	}

	public static function profileReference(Tiki_Profile_Writer $writer, $value)
	{
		return preg_replace_callback(
			'/#([0-9]+)/',
			function ($args) use ($writer) {
				return $writer->getReference('tracker_field', $args[1]);
			},
			$value
		);

	}
}
