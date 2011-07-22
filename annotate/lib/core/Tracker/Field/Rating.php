<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Rating extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'STARS' => array(
				'name' => tr('Rating'),
                                'description' => tr('A rating of the tracker item'),
                                'readonly' => true,
                                'params' => array(
                                        'option' => array(
                                                'name' => tr('Option'),
                                                'description' => tr('The possible options (comma separated integers) for the rating.'),
                                                'filter' => 'int',
                                                'count' => '*', 
                                        ),
					'mode' => array(
						'name' => tr('Mode'),
						'description' => tr('Display rating options as:'),
						'filter' => 'text',
						'options' => array(
                                                        'stars' => tr('Stars'),
                                                        'radio' => tr('Radio Buttons'),
                                                ), 
					),
					'labels' => array(
                                                'name' => tr('Labels'),
                                                'description' => tr('The text labels for the possible options.'),
                                                'filter' => 'text',
                                                'count' => '*',
                                        ),	
                                ),
                        ), 
			'*' => array(
				'name' => tr('Stars (deprecated)'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
				'deprecated' => true,
				'params' => array(
					'option' => array(
						'name' => tr('Option'),
						'description' => tr('A possible option for the rating.'),
						'filter' => 'int',
						'count' => '*',
					),
				),
			),
			's' => array(
				'name' => tr('Stars (system)'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
				'deprecated' => true,
				'params' => array(
					'option' => array(
						'name' => tr('Option'),
						'description' => tr('A possible option for the rating.'),
						'filter' => 'int',
						'count' => '*',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $user;

		$trklib = TikiLib::lib('trk');
		$ins_id = $this->getInsertId();
		$mode = 'stars'; // default is stars for legacy reasons

		$options_array = $this->getConfiguration('options_array');
		foreach ($options_array as $k => $v) {
			if (!is_numeric($v)) {
				$mode = $v;
				$labelstartkey = $k + 1;
				$rating_option_num = $k;
				break;
			}					
		}
		if ($mode == 'radio') {
			for ($i = $labelstartkey; $i < count($options_array); $i++) {
				$labels_array[] = $options_array[$i]; 
			} 
		} else {
			$labels_array = array(); 
		}
		if (isset($rating_option_num)) {
			$rating_options = array_slice($options_array, 0, $rating_option_num);
		} else {
			$rating_options = $options_array;
		}
		$data = array(
			'fieldId' => $this->getConfiguration('fieldId'),
			'type' => $this->getConfiguration('type'),
			'name' => $this->getConfiguration('name'),
			'value' => $this->getValue(), 
			'options_array' => $options_array,
			'rating_options' => $rating_options,
		);

		if (isset($requestData['vote']) && isset($requestData['itemId'])) {
			$trklib->replace_star($requestData[$ins_id], $this->getConfiguration('trackerId'), $requestData['itemId'], $data, $user, true);
		} else {
			$trklib->update_star_field($this->getConfiguration('trackerId'), $this->getItemId(), $data);
		}

		return array(
			'my_rate' => $data['my_rate'],
			'numvotes' => empty($data['numvotes']) ? 0 : $data['numvotes'],
			'voteavg' => empty($data['voteavg']) ? 0 : $data['voteavg'],
			'request_rate' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: null,
			'value' => $data['value'],
			'mode' => $mode,
			'labels' => $labels_array,      
                        'rating_options' => $rating_options,
		);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/rating.tpl', $context);
	}

	function renderInput($context = array())
	{
		if ($this->getConfiguration('type') == 's') {
			return $this->renderTemplate('trackerinput/rating.tpl', $context);
		}

		return null;
	}
}

