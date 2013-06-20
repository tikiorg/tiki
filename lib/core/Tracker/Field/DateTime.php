<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for DateTime
 * 
 * Letter key: ~f~
 *
 */
class Tracker_Field_DateTime extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'f' => array(
				'name' => tr('Date and Time'),
				'description' => tr('Provides drop-down options to accurately select a date and/or time.'),
				'help' => 'Date Tracker Field',					
				'prefs' => array('trackerfield_datetime'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'datetime' => array(
						'name' => tr('Type'),
						'description' => tr('Components to be included'),
						'filter' => 'text',
						'options' => array(
							'dt' => tr('Date and Time'),
							'd' => tr('Date only'),
						),
					),
					'startyear' => array(
						'name' => tr('Start Year'),
						'description' => tr('Year to allow selecting from'),
						'example' => '1987',
						'filter' => 'digits',
					),
					'endyear' => array(
						'name' => tr('End Year'),
						'description' => tr('Year to allow selecting to'),
						'example' => '2020',
						'filter' => 'digits',
					),
					'blankdate' => array(
						'name' => tr('Default selection'),
						'description' => tr('Indicates if blank dates should be allowed.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('Current Date'),
							'blank' => tr('Blank'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$value = $this->getValue();
		$data = array(
			'value' => empty($value) ? $this->getOption(3) == 'blank' ? '' : TikiLib::lib('tiki')->now : $value,
		);

		if (isset($requestData[$ins_id.'Month']) || isset($requestData[$ins_id.'Day']) || isset($requestData[$ins_id.'Year']) || isset($requestData[$ins_id.'Hour']) || isset($requestData[$ins_id.'Minute'])) {
			$data['value'] = TikiLib::lib('trk')->build_date($requestData, $this->getOption(0), $ins_id);
			if (empty($data['value']) && (!empty($requestData[$ins_id.'Month']) || !empty($requestData[$ins_id.'Day']) || !empty($requestData[$ins_id.'Year']) || !empty($requestData[$ins_id.'Hour']) || !empty($requestData[$ins_id.'Minute']))) {
				$data['error'] = 'y';
			}
		}

		return $data;
	}
	
	function renderInput($context = array())
	{
		global $user;

		TikiLib::lib('smarty')->assign('use_24hr_clock', TikiLib::lib('userprefs')->get_user_clock_pref($user));
		return $this->renderTemplate('trackerinput/datetime.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		$tikilib = TikiLib::lib('tiki');
		$value = $this->getConfiguration('value');

		if ($value) {
			$date = $tikilib->get_short_date($value);
			if ($this->getOption(0) == 'd') {
				return $date;
			}
			
			if ($this->getOption(0) == 't') {
				return $tikilib->get_short_time($value);
			}

			if ($context['list_mode'] == 'csv') {
				return $tikilib->get_short_datetime($value, false);
			}

			$current = $tikilib->get_short_date($tikilib->now);

			if ($date == $current) {
				return $tikilib->get_short_time($value);
			} else {
				return $tikilib->get_short_datetime($value, false);
			}
		}
	}

	function watchCompare($old, $new)
	{
		global $prefs;
		$dformat = $prefs['short_date_format'].' '.$prefs['short_time_format'];
		if ($old)
			$old = TikiLib::lib('tiki')->date_format($dformat, (int)$old);
		$new = TikiLib::lib('tiki')->date_format($dformat, (int)$new);

		return parent::watchCompare($old, $new);
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

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		return array(
			$baseKey => $typeFactory->timestamp($this->getValue()),
		);
	}

}

