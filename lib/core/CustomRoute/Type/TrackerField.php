<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute\Type;

use \TikiLib;
use Tiki\CustomRoute\Type;

/**
 * Custom route based on a tracker field
 */
class TrackerField extends Type
{
	/**
	 * @inheritdoc
	 */
	public function getParams()
	{
		return [
			'tracker' => [
				'name' => tr('Tracker'),
				'type' => 'select',
				'required' => true,
				'function' => 'getTrackers',
			],
			'tracker_field' => [
				'name' => tr('Field'),
				'type' => 'select',
				'required' => true,
				'function' => 'getTrackerFields',
				'args' => ['tracker'],
			],
		];
	}

	/**
	 * Get the list of trackers available to add a route
	 *
	 * @return array
	 */
	public function getTrackers()
	{
		$trklib = TikiLib::lib('trk');
		$trackers = $trklib->list_trackers(0, -1, 'name_asc', '');

		return ['' => ''] + $trackers['list'];
	}

	/**
	 * Get the list of tracker items available for a given tracker
	 *
	 * @param $trackerId
	 * @return array
	 */
	public function getTrackerFields($trackerId)
	{
		$trklib = TikiLib::lib('trk');
		$fields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');

		$list = ['' => ''];
		foreach ($fields['data'] as $trkField) {
			$fieldId = $trkField['fieldId'];
			$fieldName = $trkField['name'];

			$list[$fieldId] = $fieldName;
		}

		return $list;
	}
}
