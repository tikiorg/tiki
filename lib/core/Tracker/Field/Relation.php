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
			$relations = TikiLib::lib('relation')->get_relations_from('trackeritem', $this->getItemId(), $this->getOption(self::OPT_RELATION));
			foreach ($relations as $rel) {
				$data[] = $rel['type'] . ':' . $rel['itemId'];
			}
		}

		sort($data);
		return array(
			'value' => implode("\n", $data),
			'relations' => $data,
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

		$target = explode("\n", $value);

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
}

