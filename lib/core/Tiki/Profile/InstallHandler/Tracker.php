<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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

	private function getOptionMap() // {{{
	{
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
		);
	} // }}}

	private function getDefaults() // {{{
	{
		$defaults = array_fill_keys(array_keys($this->getOptionMap()), 'n');
		$defaults['name'] = '';
		$defaults['description'] = '';
		$defaults['creation_date_format'] = '';
		$defaults['modification_date_format'] = '';
		$defaults['email'] = '';
		$defaults['outboundEmail'] = '';
		$defaults['default_status'] = 'o';
		$defaults['modification_status'] = '';
		$defaults['list_default_status'] = 'o';
		$defaults['sort_default_order'] = 'asc';
		$defaults['sort_default_field'] = '';
		$defaults['restrict_start'] = '';
		$defaults['restrict_end'] = '';
		return $defaults;
	} // }}}
	
	private function getOptionConverters() // {{{
	{
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
		$values = $this->getDefaults();

		$input = $this->getData();
		$this->replaceReferences($input);

		$conversions = $this->getOptionConverters();
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

	function _export($trackerId, $profileObject) // {{{
	{
		global $trklib; require_once 'lib/trackers/trackerlib.php';
		$info = $trklib->get_tracker($trackerId);
		if (empty($info)) {
			return '';
		}
		if ($options = $trklib->get_tracker_options($trackerId)) {
			$info = array_merge($info, $options);
		}
		$optionMap = array_flip($this->getOptionMap());
		$defaults = $this->getDefaults();
		$conversions = $this->getOptionConverters();
		$ref = 'tracker_'.$trackerId;
		$res = array();
		$allow = array();
		$show = array();
		$res[] = 'objects:';
		$res[] = ' -';
		$res[] = '  type: tracker';
		$res[] = '  ref: '.$ref;
		$res[] = '  data:';
		$tab = '   ';
		$res[] = $tab.'name: '.$info['name'];
		if (!empty($info['description']))
			$res[] = $tab.'description: '.$info['description'];
		foreach ($info as $key => $value) {
			if (!empty($optionMap[$key]) && (!isset($defaults[$optionMap[$key]]) || $value != $defaults[$optionMap[$key]])) {
				if (strstr($optionMap[$key], 'allow_')) {
					$allow[] = str_replace('allow_', '', $optionMap[$key]);
				} elseif (strstr($optionMap[$key], 'show_')) {
					$show[] = str_replace('show_', '', $optionMap[$key]);
				} else if (isset($conversions[$optionMap[$key]]) && method_exists($conversions[$optionMap[$key]], 'reverse')) {
					$res[] = $tab.$optionMap[$key].': '.$conversions[$optionMap[$key]]->reverse($value);
				} else {
					$res[] = $tab.$optionMap[$key] . ': ' . $value;
				}
			}
		}
		if (!empty($allow)) {
			$res[] .= $tab.'allow: ['.implode(', ', $allow).']';
		}
		if (!empty($show)) {
			$res[] .= $tab.'show: ['.implode(', ', $show).']';
		}

		$fields = $trklib->list_tracker_fields($trackerId);
		$prof = new Tiki_Profile_InstallHandler_TrackerField($profileObject, array());
		foreach ($fields['data'] as $field) {
			$res = array_merge($res, $prof->_export($field, $profileObject));
		}
		return implode("\n", $res);
	} // }}}

}
