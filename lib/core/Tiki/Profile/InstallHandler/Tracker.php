<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Tracker extends Tiki_Profile_InstallHandler
{
	private function getData() // {{{
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists($data, array('show' => 'y', 'allow' => 'y'), true);

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	} // }}}

	public static function getOptionMap() // {{{
	{
		// Also used by TrackerOption
		return array(
			'name' => '',
			'description' => '',
			'show_status' => 'showStatus',
			'show_status_admin_only' => 'showStatusAdminOnly',
			'list_default_status' => 'defaultStatus',
			'email' => 'outboundEmail',
			'email_simplified' => 'simpleEmail',
			'default_status' => 'newItemStatus',
			'modification_status' => 'modItemStatus',
			'allow_user_see_own' => 'userCanSeeOwn',
			'allow_creator_modification' => 'writerCanModify',
			'allow_creator_deletion' => 'writerCanRemove',
			'allow_creator_group_modification' => 'writerGroupCanModify',
			'allow_creator_group_deletion' => 'writerGroupCanRemove',
			'show_creation_date' => 'showCreatedView',
			'show_list_creation_date' => 'showCreated',
			'show_modification_date' => 'showLastModifView',
			'show_list_modification_date' => 'showLastModif',
			'creation_date_format' => 'showCreatedFormat',
			'modification_date_format' => 'showLastModifFormat',
			'sort_default_field' => 'defaultOrderKey',
			'sort_default_order' => 'defaultOrderDir',
			'allow_rating' => 'useRatings',
			'allow_comments' => 'useComments',
			'allow_attachments' => 'useAttachments',
			'restrict_start' => 'start',
			'restrict_end' =>  'end',
			'hide_list_empty_fields' => 'doNotShowEmptyField',
			'allow_one_item_per_user' => 'oneUserItem',
			'section_format' => 'sectionFormat',
			'popup_fields' => 'showPopup',
			'admin_only_view' => 'adminOnlyViewEditItem',
			'use_form_classes' => 'useFormClasses',
			'form_classes' => 'formClasses',
		);
	} // }}}

	private static function getDefaults() // {{{
	{
		$defaults = array_fill_keys(array_keys(self::getOptionMap()), 'n');
		$defaults['name'] = '';
		$defaults['description'] = '';
		$defaults['creation_date_format'] = '';
		$defaults['modification_date_format'] = '';
		$defaults['email'] = '';
		$defaults['default_status'] = 'o';
		$defaults['modification_status'] = '';
		$defaults['list_default_status'] = 'o';
		$defaults['sort_default_order'] = 'asc';
		$defaults['sort_default_field'] = '';
		$defaults['restrict_start'] = '';
		$defaults['restrict_end'] = '';
		$defaults['popup_fields'] = '';
		$defaults['section_format'] = 'flat';
		return $defaults;
	} // }}}

	public static function getOptionConverters() // {{{
	{
		// Also used by TrackerOption
		return array(
			'restrict_start' => new Tiki_Profile_DateConverter,
			'restrict_end' => new Tiki_Profile_DateConverter,
			'sort_default_field' => new Tiki_Profile_ValueMapConverter(array( 'modification' => -1, 'creation' => -2, 'item' => -3 )),
			'list_default_status' => new Tiki_Profile_ValueMapConverter(array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' )),
			'default_status' => new Tiki_Profile_ValueMapConverter(array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' )),
			'modification_status' => new Tiki_Profile_ValueMapConverter(array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' )),
		);
	} // }}}

	function canInstall() // {{{
	{
		$data = $this->getData();

		// Check for mandatory fields
		if ( !isset($data['name']) ) {
			$ref = $this->obj->getRef();
			throw (new Exception('No name for tracker:' . (empty($ref) ? '' : ' ref=' . $ref)));
		}

		// Check for unknown fields
		$optionMap = $this->getOptionMap();

		$remain = array_diff(array_keys($data), array_keys($optionMap));
		if ( count($remain) ) {
			throw (new Exception('Cannot map object options: "' . implode('","', $remain) . '" for tracker:' . $data['name']));
		}

		return true;
	} // }}}

	function _install() // {{{
	{
		$values = self::getDefaults();

		$input = $this->getData();
		$this->replaceReferences($input);

		$conversions = self::getOptionConverters();
		foreach ( $input as $key => $value ) {
			if ( array_key_exists($key, $conversions) )
				$values[$key] = $conversions[$key]->convert($value);
			else
				$values[$key] = $value;
		}

		$name = $values['name'];
		$description = $values['description'];

		unset($values['name']);
		unset($values['description']);

		$optionMap = $this->getOptionMap();

		$options = array();
		foreach ( $values as $key => $value ) {
			$key = $optionMap[$key];
			$options[$key] = $value;
		}

		$trklib = TikiLib::lib('trk');

		$trackerId = $trklib->get_tracker_by_name($name);
		return $trklib->replace_tracker($trackerId, $name, $description, $options, 'y');
	} // }}}

	function export(Tiki_Profile_Writer $writer, $trackerId) // {{{
	{
		$trklib = TikiLib::lib('trk');
		$info = $trklib->get_tracker($trackerId);

		if (! $info) {
			return false;
		}

		if ($options = $trklib->get_tracker_options($trackerId)) {
			$info = array_merge($info, $options);
		}

		$data = array(
			'name' => $info['name'],
			'description' => $info['description'],
		);

		$optionMap = array_flip(self::getOptionMap());
		$defaults = self::getDefaults();
		$conversions = self::getOptionConverters();

		$allow = array();
		$show = array();

		foreach ($info as $key => $value) {
			if (empty($optionMap[$key])) {
				continue;
			}

			$optionKey = $optionMap[$key];
			$default = '';
			if (isset($defaults[$optionKey])) {
				$default = $defaults[$optionKey];
			}

			if ($value != $default) {
				if (strstr($optionKey, 'allow_')) {
					$allow[] = str_replace('allow_', '', $optionKey);
				} elseif (strstr($optionKey, 'show_')) {
					$show[] = str_replace('show_', '', $optionKey);
				} else if (isset($conversions[$optionKey]) && method_exists($conversions[$optionKey], 'reverse')) {
					$data[$optionKey] = $conversions[$optionKey]->reverse($value);
				} else {
					$data[$optionKey] = $value;
				}
			}
		}

		if (! empty($allow)) {
			$data['allow'] = $allow;
		}
		if (! empty($show)) {
			$data['show'] = $show;
		}

		$fieldReferences = array();
		foreach (array('sort_default_field', 'popup_fields') as $key) {
			if (isset($data[$key])) {
				$fieldReferences[$key] = $data[$key];
				unset($data[$key]);
			}
		}

		$reference = $writer->addObject('tracker', $trackerId, $data);

		$fields = $trklib->list_tracker_fields($trackerId);
		foreach ($fields['data'] as $field) {
			$writer->pushReference("{$reference}_{$field['permName']}");
			Tiki_Profile_InstallHandler_TrackerField::export($writer, $field);
		}

		foreach (array_filter($fieldReferences) as $key => $value) {
			$value = preg_replace_callback(
				'/(\d+)/',
				function ($match) use ($writer) {
					return $writer->getReference('tracker_field', $match[1]);
				},
				$value
			);
			$writer->pushReference("{$reference}_{$key}");
			$writer->addObject(
				'tracker_option',
				"$key-$trackerId",
				array(
					'tracker' => $writer->getReference('tracker', $trackerId),
					'name' => $key,
					'value' => $value,
				)
			);
		}

		return true;
	} // }}}

	function _export($trackerId, $profileObject) // {{{
	{
		$writer = new Tiki_Profile_Writer('temp', 'none');
		self::export($writer, $trackerId);
		return $writer->dump();
	} // }}}

}
