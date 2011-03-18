<?php

/**
 * Handler class for DateTime
 * 
 * Letter key: ~f~
 *
 */
class Tracker_Field_DateTime extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array(
			'value' => $this->getValue(TikiLib::lib('tiki')->now),
		);

		if (isset($requestData[$ins_id.'Month']) || isset($requestData[$ins_id.'Hour'])) {
			$data['value'] = TikiLib::lib('trk')->build_date($requestData, $this->getOption(0), $ins_id);
		}

		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/datetime.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		$tikilib = TikiLib::lib('tiki');
		$value = $this->getValue();

		if ($value) {
			$date = $tikilib->get_short_date($value);
			if ($this->getOption(0) == 'd') {
				return $date;
			} elseif ($this->getOption(0) == 't') {
				return $tikilib->get_short_time($value);
			} else {
				if (isset($context['list_mode']) && $context['list_mode'] == 'csv') {
					return $tikilib->get_short_datetime($value, false);
				} else {
					$current = $tikilib->get_short_date($tikilib->now);

					if ($date == $current) {
						return $tikilib->get_short_time($value);
					} else {
						return $tikilib->get_short_datetime($value, false);
					}
				}
			}
		}
	}
}

