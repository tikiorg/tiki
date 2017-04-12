<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_JsCalendar extends Tracker_Field_DateTime
{
	public static function getTypes()
	{
		return array(
			'j' => array(
				'name' => tr('Date and Time (Date Picker)'),
				'description' => tr('Provides a jQuery UI date picker to select a date and optionally time.'),
				'prefs' => array('trackerfield_jscalendar'),
				'tags' => array('advanced'),
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
					'useNow' => array(
						'name' => tr('Default value'),
						'description' => tr('Default date and time for new items'),
						'filter' => 'int',
						'options' => array(
							0 => tr('None (undefined)'),
							1 => tr('Item creation date and time'),
						),
						'legacy_index' => 1,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $prefs;
		if ($prefs['feature_jquery_ui'] !== 'y') {	// fall back to simple date field
			return parent::getFieldData($requestData);
		}

		$ins_id = $this->getInsertId();

		$value = (isset($requestData[$ins_id]))
			? $requestData[$ins_id]
			: $this->getValue();

		if (!empty($value) && !is_int((int) $value)) {	// prevent corrupted date values getting saved (e.g. from inline edit sometimes)
			$value = '';
			TikiLib::lib('errorreport')->report(tr('Date Picker Field: "%0" is not a valid internal date value', $value));
		}
		return array(
			'value' => $value,
		);
	}

	function renderInput($context = array())
	{
		global $prefs;
		if ($prefs['feature_jquery_ui'] !== 'y') {	// fall back to simple date field
			return parent::renderInput($context);
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_jscalendar');

		$params = array( 'fieldname' => $this->getInsertId());
		$params['showtime'] = $this->getOption('datetime') === 'd' ? 'n' : 'y';
		if ( empty($context['inForm'])) {
			$params['date'] = $this->getValue();
			if (empty($params['date'])) {
				$params['date'] = $this->getConfiguration('value');
			}
			if (empty($params['date']) && $this->getOption('useNow')) {
				$params['date'] = TikiLib::lib('tiki')->now;
			}
		} else {
			$params['date'] = '';
		}

		return smarty_function_jscalendar($params, $smarty);
	}
}

