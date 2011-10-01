<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
				'name' => tr('Date and Time (JSCalendar)'),
				'description' => tr('Provides drop-down options to accurately select a date and/or time.'),
				'prefs' => array('trackerfield_jscalendar'),
				'tags' => array('advanced'),
				'default' => 'n',
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
				: $this->getValue(TikiLib::lib('tiki')->now),
		);
	}

	function renderInput($context = array())
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_jscalendar');

		$params = array( 'fieldname' => $this->getInsertId());
		$params['showtime'] = $this->getOption(0) === 'd' ? 'n' : 'y';
		if ( empty($context['inForm'])) {
			$params['date'] = $this->getValue();
			if (empty($params['date'])) {
				$params['date'] = $this->getConfiguration('value');
			}
		} else {
			$params['date'] = '';
		}

		return smarty_function_jscalendar($params, $smarty);
	}
}

