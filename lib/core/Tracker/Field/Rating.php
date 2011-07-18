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
			'*' => array(
				'name' => tr('Stars'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
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

		$data = array(
			'fieldId' => $this->getConfiguration('fieldId'),
			'type' => $this->getConfiguration('type'),
			'name' => $this->getConfiguration('name'),
			'value' => $this->getValue(),
			'options_array' => $this->getConfiguration('options_array'),
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

