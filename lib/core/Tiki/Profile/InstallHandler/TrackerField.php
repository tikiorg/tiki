<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_TrackerField extends Tiki_Profile_InstallHandler
{
	private function getData() // {{{
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists($data, array('flags' => 'y'));

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	} // }}}

	static function getDefaultValues() // {{{
	{
		return array(
			'name' => '',
			'description' => '',
			'type' => 'text_field',
			'options' => '',
			'list' => 'n',
			'link' => 'n',
			'searchable' => 'n',
			'public' => 'n',
			'visible' => 'n',
			'mandatory' => 'n',
			'multilingual' => 'n',
			'order' => 1,
			'choices' => '',   //just adding this as a placeholder
			'errordesc' => '',
			'visby' => '',     //just adding this as a placeholder for now - format seems quite complex
			'editby' => '',    //just adding this as a placeholder for now - format seems quite complex
			'descparsed' => 'n',
			'validation' => '',
			'validation_param' => '',
			'validation_message' => '',
		);
	} // }}}

	static function getConverters() // {{{
	{
		return array(
			'type' => new Tiki_Profile_ValueMapConverter(
				array( // {{{
					'action' => 'x',
					'attachment' => 'A',
					'auto_increment' => 'q',
					'calendar' => 'j',
					'category' => 'e',
					'checkbox' => 'c',
					'computed' => 'C',
					'country' => 'y',
					'currency' => 'b',
					'datetime' => 'f',
					'dropdown_other' => 'D',
					'dropdown' => 'd',
					'email' => 'm',
					'files' => 'FG',
					'freetags' => 'F',
					'geographic_feature' => 'GF',
					'group' => 'g',
					'header' => 'h',
					'icon' => 'icon',
					'image' => 'i',
					'in_group' => 'N',
					'ip_address' => 'I',
					'item_link' => 'r',
					'item_list_dynamic' => 'w',
					'item_list' => 'l',
					'language' => 'LANG',
					'ldap' => 'P',
					'location' => 'G',
					'map' => 'G',
					'multiselect' => 'M',
					'numeric' => 'n',
					'page' => 'k',
					'preference' => 'p',
					'radio' => 'R',
					'relation' => 'REL',
					'stars' => 'STARS',
					'stars_old' => '*',
					'static' => 'S',
					'system' => 's',
					'text_area' => 'a',
					'text_field' => 't',
					'url' => 'L',
					'usergroups' => 'usergroups',
					'user_subscription' => 'U',
					'user' => 'u',
					'webservice' => 'W',
				)
			), // }}}
			'visible' => new Tiki_Profile_ValueMapConverter(
				array(
					'public' => 'n',
					'admin_only' => 'y',
					'admin_editable' => 'p',
					'creator_editable' => 'c',
					'immutable' => 'i',
				)
			),
		);
	} // }}}

	private static function getOptionMap() //{{{
	{
		return array(
			'type' => 'type',
			'order' => 'position',
			'visible' => 'isHidden',
			'description' => 'description',
			'descparsed' => 'descriptionIsParsed',
			'errordesc' => 'errorMsg',
			'list' => 'isTblVisible',
			'link' => 'isMain',
			'searchable' => 'isSearchable',
			'public' => 'isPublic',
			'mandatory' => 'isMandatory',
			'multilingual' => 'isMultilingual',
			'validation' => 'validation',
			'validation_param' => 'validationParam',
			'validation_message' => 'validationMessage',
		);
	} // }}}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['name'], $data['tracker'] ) )
			return false;

		return true;
	}

	function _install()
	{
		$data = $this->getData();
		$converters = self::getConverters();
		$this->replaceReferences($data);

		foreach ( $data as $key => &$value ) {
			if ( isset( $converters[$key] ) ) {
				$value = $converters[$key]->convert($value);
			}
		}

		$data = array_merge(
			self::getDefaultValues(),
			array(
				'permname' => $this->obj->getRef(), // Use the profile reference as the name by default
			),
			$data
		);

		$trklib = TikiLib::lib('trk');

		$fieldId = $trklib->get_field_id($data['tracker'], $data['name']);

		if (! $fieldId && isset($data['permname'])) {
			$fieldId = $trklib->get_field_id($data['tracker'], $data['name'], 'permName');
		}

		$factory = new Tracker_Field_Factory;
		$fieldInfo = $factory->getFieldInfo($data['type']);
		if (!is_array($data['options'])) {
			$options = Tracker_Options::fromString($data['options'], $fieldInfo);
		} else {
			$options = Tracker_Options::fromArray($data['options'], $fieldInfo);
		}

		return $trklib->replace_tracker_field(
			$data['tracker'],
			$fieldId,
			$data['name'],
			$data['type'],
			$data['link'],
			$data['searchable'],
			$data['list'],
			$data['public'],
			$data['visible'],
			$data['mandatory'],
			$data['order'],
			$options->serialize(),
			$data['description'],
			$data['multilingual'],
			$data['choices'],
			$data['errordesc'],
			$data['visby'],
			$data['editby'],
			$data['descparsed'],
			$data['validation'],
			$data['validation_param'],
			$data['validation_message'],
			$data['permname']
		);
	}

	static function export(Tiki_Profile_Writer $writer, $field)
	{
		if (! is_array($field)) {
			$trklib = TikiLib::lib('trk');
			$field = $trklib->get_tracker_field($field);

			if (! $field) {
				return false;
			}
		}

		$factory = new Tracker_Field_Factory;
		$fieldInfo = $factory->getFieldInfo($field['type']);

		$options = Tracker_Options::fromSerialized($field['options'], $fieldInfo);
		$optionsData = array_filter($options->getAllParameters());

		foreach ($optionsData as $key => $value) {
			$paramInfo = $options->getParamDefinition($key);
			if (isset($paramInfo['profile_reference'])) {
				$optionsData[$key] = $writer->getReference($paramInfo['profile_reference'], $value);
			}
		}

		$data = array(
			'name' => $field['name'],
			'permname' => $field['permName'],
			'tracker' => $writer->getReference('tracker', $field['trackerId']),
			'options' => $optionsData,
		);

		$optionMap = array_flip(self::getOptionMap());
		$defaults = self::getDefaultValues();
		$conversions = self::getConverters();

		$flag = array();

		foreach ($field as $key => $value) {
			if (empty($optionMap[$key])) {
				continue;
			}

			$optionKey = $optionMap[$key];
			$default = '';
			if (isset($defaults[$optionKey])) {
				$default = $defaults[$optionKey];
			}

			if ($value != $default) {
				if (in_array($optionKey, array('list', 'link', 'searchable', 'public', 'mandatory', 'multilingual'))) {
					if (!empty($value)) {
						$flag[] = $optionKey;
					}
				} elseif (!empty($conversions[$optionKey])) {
					$reverseVal = $conversions[$optionKey]->reverse($value);
					$data[$optionKey] = $reverseVal;
				} else {
					$data[$optionKey] = $value;
				}
			}
		}

		if (!empty($flag)) {
			$data['flags'] = $flag;
		}

		$writer->addObject('tracker_field', $field['fieldId'], $data);

		return true;
	}
}
