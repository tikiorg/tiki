<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Relation extends Tracker_Field_Abstract
{
	const OPT_RELATION = 'relation';
	const OPT_FILTER = 'filter';
	const OPT_READONLY = 'readonly';
	const OPT_INVERT = 'invert';

	public static function getTypes()
	{
		return array(
			'REL' => array(
				'name' => tr('Relations'),
				'description' => tr('Allows to create arbitrary relations between the trackers and other objects in the system.'),
				'prefs' => array('trackerfield_relation'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'relation' => array(
						'name' => tr('Relation'),
						'description' => tr('Relation qualifier. Must be a three-part qualifier containing letters and separated by dots.'),
						'filter' => 'attribute_type',
						'legacy_index' => 0,
					),
					'filter' => array(
						'name' => tr('Filter'),
						'description' => tr('URL-encoded list of filters to be applied on object selection.'),
						'filter' => 'url',
						'legacy_index' => 1,
						'profile_reference' => 'search_urlencoded',
					),
					'readonly' => array(
						'name' => tr('Read-only'),
						'description' => tr('Only display the incoming relations instead of manipulating them.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 2,
					),
					'invert' => array(
						'name' => tr('Include Invert'),
						'description' => tr('Include invert relations in the list'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 3,
					),
					'display' => array(
						'name' => tr('Display'),
						'description' => tr('Control how the relations are displayed in view mode'),
						'filter' => 'word',
						'options' => array(
							'list' => tr('List'),
							'count' => tr('Count'),
							'toggle' => tr('Count with toggle for list'),
						),
						'legacy_index' => 4,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$insertId = $this->getInsertId();

		$data = array();
		if (! $this->getOption(self::OPT_READONLY) && isset($requestData[$insertId])) {
			if (is_string($requestData[$insertId])) {
				$data = explode("\n", $requestData[$insertId]);
				$data = array_filter($data);
			} else {
				$data = (array) $requestData[$insertId];
			}
			$data = array_unique($data);
		} else {
			$data = $this->getRelations($this->getOption(self::OPT_RELATION));
		}

		if ($this->getOption(self::OPT_INVERT)) {
			$inverts = array_diff(
				$this->getRelations($this->getOption(self::OPT_RELATION) . '.invert'),
				$data
			);
		} else {
			$inverts = array();
		}

		return array(
			'value' => implode("\n", $data),
			'relations' => $data,
			'inverts' => $inverts,
		);
	}

	function renderInput($context = array())
	{
		if ($this->getOption(self::OPT_READONLY)) {
			return tra('Read-only');
		}

		$labels = array();
		foreach ($this->getConfiguration('relations') as $rel) {
			list($type, $id) = explode(':', $rel, 2);
			$labels[$rel] = TikiLib::lib('object')->get_title($type, $id);
		}
		foreach ($this->getConfiguration('inverts') as $rel) {
			list($type, $id) = explode(':', $rel, 2);
			$labels[$rel] = TikiLib::lib('object')->get_title($type, $id);
		}

		$filter = $this->buildFilter();

		return $this->renderTemplate(
			'trackerinput/relation.tpl',
			$context,
			array(
				'labels' => $labels,
				'filter' => $filter,
			)
		);
	}

	function renderOutput($context = array())
	{
		if ($context['list_mode'] === 'csv') {
			return $this->getConfiguration('value');
		} else {
			$display = $this->getOption('display');
			if (! in_array($display, array('list', 'count', 'toggle'))) {
				$display = 'list';
			}

			return $this->renderTemplate('trackeroutput/relation.tpl', $context, array(
				'display' => $display,
			));
		}
	}

	function handleSave($value, $oldValue)
	{
		if ($this->getOption(self::OPT_READONLY)) {
			return array(
				'value' => $value,
			);
		}

		$relationlib = TikiLib::lib('relation');
		$current = $relationlib->get_relations_from('trackeritem', $this->getItemId(), $this->getOption(self::OPT_RELATION));
		$map = array();
		foreach ($current as $rel) {
			$key = $rel['type'] . ':' . $rel['itemId'];
			$id = $rel['relationId'];
			$map[$key] = $id;
		}

		if ($value) {
			$target = explode("\n", trim($value));
		} else {
			$target = array();
		}
		$toRemove = array_diff(array_keys($map), $target);
		$toAdd = array_diff($target, array_keys($map));

		foreach ($toRemove as $value) {
			$id = $map[$value];
			$relationlib->remove_relation($id);
		}

		foreach ($toAdd as $key) {
			list($type, $id) = explode(':', $key, 2);

			$relationlib->add_relation($this->getOption(self::OPT_RELATION), 'trackeritem', $this->getItemId(), $type, $id);
		}

		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
	}

	private function buildFilter()
	{
		parse_str($this->getOption(self::OPT_FILTER), $filter);
		return $filter;
	}

	private function getRelations($relation)
	{
		$data = array();
		$relations = TikiLib::lib('relation')->get_relations_from('trackeritem', $this->getItemId(), $relation);
		foreach ($relations as $rel) {
			$data[] = $rel['type'] . ':' . $rel['itemId'];
		}

		return $data;
	}
}

