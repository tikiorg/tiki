<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_DateTime extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable
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
						'legacy_index' => 0,
					),
					'startyear' => array(
						'name' => tr('Start Year'),
						'description' => tr('Year to allow selecting from'),
						'example' => '1987',
						'filter' => 'digits',
						'legacy_index' => 1,
					),
					'endyear' => array(
						'name' => tr('End Year'),
						'description' => tr('Year to allow selecting to'),
						'example' => '2020',
						'filter' => 'digits',
						'legacy_index' => 2,
					),
					'blankdate' => array(
						'name' => tr('Default selection'),
						'description' => tr('Indicates if blank dates should be allowed.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('Current Date'),
							'blank' => tr('Blank'),
						),
						'legacy_index' => 3,
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
			'value' => empty($value) ? ($this->getOption('blankdate') == 'blank' ? '' : TikiLib::lib('tiki')->now) : $value,
		);

		if (isset($requestData[$ins_id.'Month']) || isset($requestData[$ins_id.'Day']) || isset($requestData[$ins_id.'Year']) || isset($requestData[$ins_id.'Hour']) || isset($requestData[$ins_id.'Minute'])) {
			$data['value'] = TikiLib::lib('trk')->build_date($requestData, $this->getOption('datetime'), $ins_id);
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
			$date = $tikilib->get_short_date($value, $user);
			if ($this->getOption('datetime') == 'd') {
				return $date;
			}
			
			if ($this->getOption('datetime') == 't') {
				return $tikilib->get_short_time($value, $user);
			}

			if ($context['list_mode'] == 'csv') {
				return $tikilib->get_short_datetime($value, $user);
			}

			$current = $tikilib->get_short_date($tikilib->now, $user);
			global $prefs;

			if ($date == $current && $prefs['tiki_same_day_time_only'] == 'y' ) {
				return $tikilib->get_short_time($value, $user);
			} else {
				return $tikilib->get_short_datetime($value, $user);
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

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		return array(
			$baseKey => $typeFactory->timestamp($this->getValue()),
		);
	}

	function getTabularSchema()
	{
		$permName = $this->getConfiguration('permName');
		$type = $this->getOption('datetime');

		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$label = $this->getConfiguration('name');
		$helper = new Tracker\Tabular\Schema\DateHelper($label);
		$helper->setupUnix($schema->addNew($permName, 'unix'));

		if ($type == 'd') {
			$helper->setupFormat('Y-m-d', $schema->addNew($permName, 'yyyy-mm-dd'));
		} else {
			$helper->setupFormat('Y-m-d H:i:s', $schema->addNew($permName, 'yyyy-mm-dd hh:mm:ss'));
		}

		return $schema;
	}

}

