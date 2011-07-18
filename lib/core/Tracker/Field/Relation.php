<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Relation extends Tracker_Field_Abstract
{
	const OPT_RELATION = 0;
	const OPT_FILTER = 1;
	const OPT_READONLY = 2;
	const OPT_INVERT = 3;

	public static function getTypes()
	{
		return array(
			'REL' => array(
				'name' => tr('Relations'),
				'description' => tr('Allows to create arbitrary relations between the trackers and other objects in the system.'),
				'params' => array(
					'relation' => array(
						'name' => tr('Relation'),
						'description' => tr('Relation qualifier. Must be a three-part qualifier containing letters and separated by dots.'),
						'filter' => 'attribute_type',
					),
					'filter' => array(
						'name' => tr('Filter'),
						'description' => tr('URL-encoded list of filters to be applied on object selection.'),
						'filter' => 'url',
					),
					'readonly' => array(
						'name' => tr('Read-only'),
						'description' => tr('Only display the incoming relations instead of manipulating them.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'invert' => array(
						'name' => tr('Include Invert'),
						'description' => tr('Include invert relations in the list'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
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
			return tra('Read only');
		}

		$context['labels'] = array();
		foreach ($this->getConfiguration('relations') as $rel) {
			list($type, $id) = explode(':', $rel, 2);
			$context['labels'][$rel] = TikiLib::lib('object')->get_title($type, $id);
		}
		foreach ($this->getConfiguration('inverts') as $rel) {
			list($type, $id) = explode(':', $rel, 2);
			$context['labels'][$rel] = TikiLib::lib('object')->get_title($type, $id);
		}
		$context['filter'] = $this->buildFilter();
		return $this->renderTemplate('trackerinput/relation.tpl', $context);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/relation.tpl', $context);
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
			$target = explode("\n", $value);
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

