<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_Controller
{
	function action_add_field($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trklib = TikiLib::lib('trk');
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$name = $input->name->text();
		$type = $input->type->text();
		$description = $input->description->text();
		$wikiparse = $input->description_parse->int();
		$fieldId = 0;

		$types = $this->getFieldTypes($description);

		if (empty($type)) {
			$type = 't';
		}

		if (! isset($types[$type])) {
			throw new Services_Exception(tr('Type does not exist'), 400);
		}

		if ($input->type->word()) {
			if (empty($name)) {
				throw new Services_Exception(tr('Field name cannot be empty'), 409);
			}

			if ($trklib->get_field_id($trackerId, $name)) {
				throw new Services_Exception(tr('Field name already exists in the tracker'), 409);
			}

			$fieldId = $this->createField(array(
				'trackerId' => $trackerId,
				'name' => $name,
				'type' => $type,
				'description' => $description,
				'descriptionIsParsed' => $wikiparse,
			));
		}

		return array(
			'trackerId' => $trackerId,
			'fieldId' => $fieldId,
			'name' => $name,
			'type' => $type,
			'types' => $types,
			'description' => $description,
			'descriptionIsParsed' => $wikiparse,
		);
	}

	function action_list_fields($input)
	{
		$trackerId = $input->trackerId->int();
		$perms = Perms::get('tracker', $trackerId);

		if (! $perms->view_trackers) {
			throw new Services_Exception(tr('Not allowed to view the tracker'), 403);
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		return array(
			'fields' => $definition->getFields(),
			'types' => $this->getFieldTypes(),
		);
	}

	function action_save_fields($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$fields = array();
		foreach ($input->field as $key => $value) {
			$fieldId = (int) $key;
			$fields[$fieldId] = array(
				'position' => $value->position->int(),
				'isTblVisible' => $value->isTblVisible->int() ? 'y' : 'n',
				'isMain' => $value->isMain->int() ? 'y' : 'n',
				'isSearchable' => $value->isSearchable->int() ? 'y' : 'n',
				'isPublic' => $value->isPublic->int() ? 'y' : 'n',
				'isMandatory' => $value->isMandatory->int() ? 'y' : 'n',
			);

			$this->updateField($trackerId, $fieldId, $fields[$fieldId]);
		}

		return array(
			'fields' => $fields,
		);
	}

	function action_edit_field($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trackerId = $input->trackerId->int();
		$fieldId = $input->fieldId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$field = $definition->getField($fieldId);
		if (! $field) {
			throw new Services_Exception(tr('Tracker field not found in specified tracker'), 404);
		}

		$types = $this->getFieldTypes($description);
		$typeInfo = $types[$field['type']];

		if ($input->name->text()) {
			$this->updateField($trackerId, $fieldId, array(
				'name' => $input->name->text(),
				'description' => $input->description->text(),
				'descriptionIsParsed' => $input->description_parse->int() ? 'y' : 'n',
				'options' => $this->buildOptions($input->option, $typeInfo),
			));
		}

		return array(
			'field' => $field,
			'info' => $typeInfo,
			'options' => $this->parseOptions($field['options_array'], $typeInfo),
		);
	}

	private function buildOptions($input, $typeInfo)
	{
		$parts = array();

		foreach ($typeInfo['params'] as $key => $info) {
			$filter = $info['filter'];

			if (isset($info['count']) && $info['count'] === '*') {
				$values = explode(',', $input->$key->none());
				$filter = TikiFilter::get($filter);
				$values = array_map(array($filter, 'filter'), $values);
				$values = array_filter($values);
			} else {
				$values = array($input->$key->$filter());
			}

			foreach ($values as $value) {
				if (isset($info['options']) && ! isset($info['options'][$value])) {
					$value = null;
				}

				$parts[] = $value;
			}
		}

		$rawOptions = implode(',', $parts);
		return rtrim($rawOptions, ',');
	}

	private function parseOptions($raw, $typeInfo)
	{
		$out = array();

		foreach ($typeInfo['params'] as $key => $info) {
			if (isset($info['count']) && $info['count'] === '*') {
				$out[$key] = implode(',', $raw);
			} else {
				$out[$key] = array_shift($raw);
			}
		}

		return $out;
	}

	private function getFieldTypes($description)
	{
		$factory = new Tracker_Field_Factory($description);
		return $factory->getFieldTypes();
	}

	private function createField(array $data)
	{
		$trklib = TikiLib::lib('trk');
		return $trklib->replace_tracker_field(
			$data['trackerId'],
			0,
			$data['name'],
			$data['type'],
			'n',
			'n',
			'n',
			'y',
			'n',
			'y',
			$trklib->get_last_position($data['trackerId']) + 10,
			'',
			$data['description'],
			'',
			null,
			'',
			null,
			null,
			$data['descriptionIsParsed'] ? 'y' : 'n');
	}

	private function updateField($trackerId, $fieldId, array $properties)
	{
		$definition = Tracker_Definition::get($trackerId);

		$field = $definition->getField($fieldId);
		$trklib = TikiLib::lib('trk');
		$trklib->replace_tracker_field(
			$trackerId,
			$fieldId,
			isset($properties['name']) ? $properties['name'] : $field['name'],
			$field['type'],
			isset($properties['isMain']) ? $properties['isMain'] : $field['isMain'],
			isset($properties['isSearchable']) ? $properties['isSearchable'] : $field['isSearchable'],
			isset($properties['isTblVisible']) ? $properties['isTblVisible'] : $field['isTblVisible'],
			isset($properties['isPublic']) ? $properties['isPublic'] : $field['isPublic'],
			$field['isHidden'],
			isset($properties['isMandatory']) ? $properties['isMandatory'] : $field['isMandatory'],
			isset($properties['position']) ? $properties['position'] : $field['position'],
			isset($properties['options']) ? $properties['options'] : $field['options'],
			isset($properties['description']) ? $properties['description'] : $field['description'],
			$field['isMultilingual'],
			$field['itemChoices'],
			$field['errorMsg'],
			$field['visibleBy'],
			$field['editableBy'],
			isset($properties['descriptionIsParsed']) ? $properties['descriptionIsParsed'] : $field['descriptionIsParsed'],
			$field['validation'],
			$field['validationParam'],
			$field['validationMessage']
		);
	}
}

