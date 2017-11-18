<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Rating extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return [
			'STARS' => [
				'name' => tr('Rating'),
				'description' => tr('A rating of the tracker item. Permissions involved: %0', 'tracker_vote_ratings, tracker_revote_ratings, tracker_view_ratings'),
				'readonly' => true,
				'help' => 'Rating Tracker Field',
				'prefs' => ['trackerfield_rating'],
				'tags' => ['advanced'],
				'default' => 'n',
				'params' => [
					'option' => [
						'name' => tr('Option'),
						'description' => tr('The possible options (comma-separated integers) for the rating.'),
						'filter' => 'int',
						'count' => '*',
						'legacy_index' => 0,
					],
					'mode' => [
						'name' => tr('Mode'),
						'description' => tr('Display rating options as:'),
						'filter' => 'text',
						'options' => [
							'stars' => tr('Stars'),
							'radio' => tr('Radio Buttons'),
							'like' => tr('Single Option: for example, Like'),
						],
						'legacy_index' => 1,
					],
					'labels' => [
						'name' => tr('Labels'),
						'description' => tr('The text labels (comma-separated) for the possible options.'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 2,
					],
				],
			],
			'*' => [
				'name' => tr('Stars (deprecated)'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
				'deprecated' => true,
				'prefs' => ['trackerfield_stars'],
				'tags' => ['experimental'],
				'default' => 'n',
				'params' => [
					'option' => [
						'name' => tr('Option'),
						'description' => tr('A possible option for the rating.'),
						'filter' => 'int',
						'count' => '*',
						'legacy_index' => 0,
					],
				],
			],
			's' => [
				'name' => tr('Stars (system - deprecated)'),
				'description' => tr('Displays a star rating'),
				'readonly' => true,
				'deprecated' => true,
				'prefs' => ['trackerfield_starsystem'],
				'tags' => ['experimental'],
				'default' => 'n',
				'params' => [
					'option' => [
						'name' => tr('Option'),
						'description' => tr('A possible option for the rating.'),
						'filter' => 'int',
						'count' => '*',
						'legacy_index' => 0,
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		$ins_id = $this->getInsertId();

		$result = null;
		if (isset($requestData['vote']) && isset($requestData['itemId'])) {
			$trklib = TikiLib::lib('trk');
			$data = $this->getBaseFieldData();
			global $user;
			$result = $trklib->replace_star($requestData[$ins_id], $this->getConfiguration('trackerId'), $requestData['itemId'], $data, $user, true);
		} else {
			$data = $this->gatherVoteData();
		}

		return [
			'my_rate' => $data['my_rate'],
			'numvotes' => empty($data['numvotes']) ? 0 : $data['numvotes'],
			'voteavg' => empty($data['voteavg']) ? 0 : $data['voteavg'],
			'request_rate' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: null,
			'value' => $data['value'],
			'mode' => $data['mode'],
			'labels' => $data['labels_array'],
			'rating_options' => $data['rating_options'],
			'result' => $result,
		];
	}

	function renderOutput($context = [])
	{
		return $this->renderTemplate('trackeroutput/rating.tpl', $context);
	}

	function renderInput($context = [])
	{
		if ($this->getConfiguration('type') == 's') {
			return $this->renderTemplate('trackerinput/rating.tpl', $context);
		} else {
			$data = $this->gatherVoteData();
			$str = tra("Number of votes:") . ' ' . $data['numvotes'] . ', ' . tra('Average:') . ' ' . $data['voteavg'];
			if (! empty($data['my_rate'])) {
				$str .= ' (' . tra("Your rating:") . ' ' . $data['my_rate'] . ')';
			}
			return $str;
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$data = $this->gatherVoteData();
		$baseKey = $this->getBaseKey();

		return [
			$baseKey => $typeFactory->numeric($data['voteavg']),
			"{$baseKey}_count" => $typeFactory->numeric($data['numvotes']),
			"{$baseKey}_sum" => $typeFactory->numeric($data['total']),
		];
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return [
			$baseKey,
			"{$baseKey}_count",
			"{$baseKey}_sum",
		];
	}

	function getGlobalFields()
	{
		return [];
	}

	private function getBaseFieldData()
	{
		global $user;

		$mode = $this->getOption('mode', 'stars');

		$options_array = $this->getOption('option', [1, 2, 3, 4, 5]);
		$labels_array = $this->getOption('labels', $options_array);
		if ($mode == 'stars') {
			$labels_array = [];
		}

		if ($mode == 'like') {
			$rating_options = [0,1];
		} else {
			$rating_options = $options_array;
		}

		return [
			'fieldId' => $this->getConfiguration('fieldId'),
			'type' => $this->getConfiguration('type'),
			'name' => $this->getConfiguration('name'),
			'value' => $this->getValue(),
			'options_array' => $options_array,
			'rating_options' => $rating_options,
			'labels_array' => $labels_array,
			'mode' => $mode,
		];
	}

	private function gatherVoteData()
	{
		global $user;
		$field = $this->getBaseFieldData();
		$trackerId = $this->getConfiguration('trackerId');
		$itemId = $this->getItemId();

		$votings = TikiDb::get()->table('tiki_user_votings');

//		if ($field['type'] == 's' && $field['name'] == tra('Rating')) { // global rating to an item - value is the sum of the votes
		if ($field['type'] == 's') { // global rating to an item - value is the sum of the votes. No need for hardcoded value Rating, internationalized or not: admins can replace for a more suited word for their use case.
			$key = 'tracker.' . $trackerId . '.' . $itemId;
		} elseif ($field['type'] == '*' || $field['type'] == 'STARS') { // field rating - value is the average of the votes
			$key = "tracker.$trackerId.$itemId." . $field['fieldId'];
		}

		$data = $votings->fetchRow(
			[
				'count' => $votings->count(),
				'total' => $votings->sum('optionId'),
			],
			['id' => $key]
		);

		$field['numvotes'] = $data['count'];
		$field['total'] = $data['total'];
		if ($field['numvotes']) {
			$field['voteavg'] = round($field['total'] / $field['numvotes'], 2);
		} else {
			$field['voteavg'] = 0;
		}
		// be careful optionId is the value - not the optionId
		$field['my_rate'] = $votings->fetchOne('optionId', ['id' => $key, 'user' => $user]);

		return $field;
	}
}
