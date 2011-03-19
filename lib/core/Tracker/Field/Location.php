<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for location/map/gmap
 * 
 * Letter key: ~G~
 *
 */
class Tracker_Field_Location extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		if (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue();
		}
		
		$parts = explode(',', $value);
		$parts = array_map('floatval', $parts);

		if (count($parts) >= 2) {
			return array(
				'value' => implode(',', $parts),
				'x' => $parts[0],
				'y' => $parts[1],
				'z' => isset($parts[2]) ? $parts[2] : 0,
			);
		} else {
			return array(
				'value' => '',
				'x' => null,
				'y' => null,
				'z' => null,
			);
		}
	}

	function renderInput($context = array())
	{
		TikiLib::lib('header')->add_map();
		return $this->renderTemplate('trackerinput/location.tpl', $context);
	}

	function renderOutput($context = array())
	{
		TikiLib::lib('header')->add_map();
		return $this->renderTemplate('trackeroutput/location.tpl', $context);
	}
}

