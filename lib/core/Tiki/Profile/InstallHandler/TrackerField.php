<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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

	function getDefaultValues() // {{{
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
			'permname' => $this->obj->getRef(), // Use the profile reference as the name by default
		);
	} // }}}

	function getConverters() // {{{
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
	private function getOptionMap() //{{{
	{
		return array(
			'type' => 'type',
			'order' => 'position',
			'visible' => 'isHidden',
			'description' => 'description',
			'descparsed' => 'descriptionIsParsed',
			'errordesc' => 'errorMsg',
			'list' => 'IsTblVisible',
			'link' => 'isMain',
			'searchable' => 'isSearchable',
			'public' => 'isPublic',
			'mandatory' => 'isMandatory',
			'multilingual' => 'isMultilingual',
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
		$converters = $this->getConverters();
		$this->replaceReferences($data);

		foreach ( $data as $key => &$value )
			if ( isset( $converters[$key] ) )
				$value = $converters[$key]->convert($value);

		$data = array_merge($this->getDefaultValues(), $data);

		$trklib = TikiLib::lib('trk');

		$fieldId = $trklib->get_field_id($data['tracker'], $data['name']);

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
			$data['options'],
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

	function _export($info)
	{
		$optionMap = array_flip($this->getOptionMap());
		$defaults = $this->getDefaultValues();
		$conversions = $this->getConverters();
		$res[] = ' -';
		$refi = 'field_'.$info['fieldId'];
		$res[] = '  type: tracker_field';
		$res[] = '  ref: '. $refi;
		$res[] = '  data:';
		$res[] = '   name: '.$info['name'];
		$res[] = '   tracker: $tracker_'.$info['trackerId'];
		if (!empty($info['options'])) $res[] = '   options: '.$info['options'];
		$flag = array();
		$tab = '   ';
		foreach ($info as $key => $value) {
			if (!empty($optionMap[$key]) && (!isset($defaults[$optionMap[$key]]) || $value != $defaults[$optionMap[$key]])) {
				if (in_array($optionMap[$key], array('list', 'link', 'searchable', 'public', 'mandatory', 'multilingual'))) {
					if (!empty($value)) {
						$flag[] = $optionMap[$key];
					}
				} elseif (!empty($conversions[$optionMap[$key]])) {
					$reverseVal = $conversions[$optionMap[$key]]->reverse($value);
					$res[] = $tab.$optionMap[$key].': '.(empty($reverseVal)? $value: $reverseVal);
				} else {
					$res[] = $tab.$optionMap[$key].': '.$value;
				}
			}
		}
		if (!empty($flag)) {
				$res[] .= $tab.'flags: ['.implode(', ', $flag).']';
		}
		return $res;
	}
}
