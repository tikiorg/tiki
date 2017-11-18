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
		return [
			'j' => [
				'name' => tr('Date and Time (Date Picker)'),
				'description' => tr('Provides a jQuery UI date picker to select a date and optionally time.'),
				'prefs' => ['trackerfield_jscalendar'],
				'tags' => ['advanced'],
				'default' => 'y',
				'supported_changes' => ['f', 'j'],
				'params' => [
					'datetime' => [
						'name' => tr('Type'),
						'description' => tr('Components to be included'),
						'filter' => 'text',
						'options' => [
							'dt' => tr('Date and Time'),
							'd' => tr('Date only'),
						],
						'legacy_index' => 0,
					],
					'useNow' => [
						'name' => tr('Default value'),
						'description' => tr('Default date and time for new items'),
						'filter' => 'int',
						'options' => [
							0 => tr('None (undefined)'),
							1 => tr('Item creation date and time'),
						],
						'legacy_index' => 1,
					],
					'useTimeAgo' => [
						'name' => tr('Time Ago'),
						'description' => tr('Use timeago.js if the feature is enabled'),
						'filter' => 'int',
						'options' => [
							0 => tr('No'),
							1 => tr('Yes'),
						],
					],
					'notBefore' => [
						'name' => tr('Not before'),
						'description' => tr('Field ID from this tracker to compare the value against and validate it is not before that timestamp.'),
						'filter' => 'int',
						'legacy_index' => 2,
						'profile_reference' => 'tracker_field',
						'parent' => 'input[name=trackerId]',
						'parentkey' => 'tracker_id',
						'sort_order' => 'position_nasc',
					],
					'notAfter' => [
						'name' => tr('Not after'),
						'description' => tr('Field ID from this tracker to compare the value against and validate it is not after that timestamp.'),
						'filter' => 'int',
						'legacy_index' => 3,
						'profile_reference' => 'tracker_field',
						'parent' => 'input[name=trackerId]',
						'parentkey' => 'tracker_id',
						'sort_order' => 'position_nasc',
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		global $prefs;
		if ($prefs['feature_jquery_ui'] !== 'y') {	// fall back to simple date field
			return parent::getFieldData($requestData);
		}

		$ins_id = $this->getInsertId();

		$value = (isset($requestData[$ins_id]))
			? $requestData[$ins_id]
			: $this->getValue();

		if (! empty($value) && ! is_int((int) $value)) {	// prevent corrupted date values getting saved (e.g. from inline edit sometimes)
			$value = '';
			Feedback::error(tr('Date Picker Field: "%0" is not a valid internal date value', $value), 'session');
		}

		// if local browser offset is submitted, convert timestamp to server-based timezone
		if (isset($requestData['tzoffset']) && $value && isset($requestData[$ins_id])) {
			$browser_offset = 0 - intval($requestData['tzoffset']) * 60;

			$server_offset = TikiDate::tzServerOffset(TikiLib::lib('tiki')->get_display_timezone());

			$value = $value - $server_offset + $browser_offset;
		}

		return [
			'value' => $value,
		];
	}

	function renderInput($context = [])
	{
		global $prefs;
		if ($prefs['feature_jquery_ui'] !== 'y') {	// fall back to simple date field
			return parent::renderInput($context);
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_jscalendar');

		$params = [ 'fieldname' => $this->getConfiguration('ins_id') ? $this->getConfiguration('ins_id') : $this->getInsertId()];
		$params['showtime'] = $this->getOption('datetime') === 'd' ? 'n' : 'y';
		if (empty($context['inForm'])) {
			$params['date'] = $this->getValue();
			if (empty($params['date'])) {
				$params['date'] = $this->getConfiguration('value');
			}
			if (empty($params['date']) && $this->getOption('useNow')) {
				$params['date'] = TikiLib::lib('tiki')->now;
			}
		} else {
			$params['date'] = $this->getValue();
		}

		if ($params['date']) {
			// convert to UTC to display it properly for browser based timezone
			$params['date'] += TikiDate::tzServerOffset(TikiLib::lib('tiki')->get_display_timezone());
			$params['isutc'] = true;
		}

		return smarty_function_jscalendar($params, $smarty);
	}

	function isValid($ins_fields_data)
	{
		if ($notBefore = $this->getOption('notBefore')) {
			$notBeforeTimestamp = $ins_fields_data[$notBefore]['value'];
			if ((string)$notBeforeTimestamp !== (string)intval($notBeforeTimestamp)) {
				$notBeforeTimestamp = strtotime($notBeforeTimestamp);
			}
			if (! $notBeforeTimestamp || $this->getValue() < $notBeforeTimestamp) {
				return tr('%0 cannot be before %1', $this->getConfiguration('name'), $ins_fields_data[$notBefore]['name']);
			}
		}

		if ($notAfter = $this->getOption('notAfter')) {
			$notAfterTimestamp = $ins_fields_data[$notAfter]['value'];
			if ((string)$notAfterTimestamp !== (string)intval($notAfterTimestamp)) {
				$notAfterTimestamp = strtotime($notAfterTimestamp);
			}
			if (! $notAfterTimestamp || $this->getValue() > $notAfterTimestamp) {
				return tr('%0 cannot be after %1', $this->getConfiguration('name'), $ins_fields_data[$notAfter]['name']);
			}
		}

		return true;
	}
}
