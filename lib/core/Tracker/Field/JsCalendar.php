<?php

class Tracker_Field_JsCalendar extends Tracker_Field_DateTime
{
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
		return $this->renderTemplate('trackerinput/jscalendar.tpl');
	}
}

