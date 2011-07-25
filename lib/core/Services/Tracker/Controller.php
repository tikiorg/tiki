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
		$permName = $input->permName->word();
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
				throw new Services_Exception_MissingValue('name');
			}

			if ($definition->getFieldFromName($name)) {
				throw new Services_Exception_DuplicateValue('name', $name);
			}

			if ($definition->getFieldFromPermName($permName)) {
				throw new Services_Exception_DuplicateValue('permName', $permName);
			}

			$fieldId = $this->createField(array(
				'trackerId' => $trackerId,
				'name' => $name,
				'permName' => $permName,
				'type' => $type,
				'description' => $description,
				'descriptionIsParsed' => $wikiparse,
			));
		}

		return array(
			'trackerId' => $trackerId,
			'fieldId' => $fieldId,
			'name' => $name,
			'permName' => $permName,
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

		$permName = $input->permName->word();
		if ($field['permName'] != $permName) {
			if ($definition->getFieldFromPermName($permName)) {
				throw new Services_Exception_DuplicateValue('permName', $permName);
			}
		}

		if ($input->name->text()) {
			$input->replaceFilters(array(
				'visible_by' => 'groupname',
				'editable_by' => 'groupname',
			));
			$visibleBy = $input->asArray('visible_by', ',');
			$editableBy = $input->asArray('editable_by', ',');
			$this->updateField($trackerId, $fieldId, array(
				'name' => $input->name->text(),
				'description' => $input->description->text(),
				'descriptionIsParsed' => $input->description_parse->int() ? 'y' : 'n',
				'options' => $this->buildOptions($input->option, $typeInfo),
				'validation' => $input->validation_type->word(),
				'validationParam' => $input->validation_parameter->none(),
				'validationMessage' => $input->validation_message->text(),
				'isMultilingual' => $input->multilingual->int() ? 'y' : 'n',
				'visibleBy' => array_filter(array_map('trim', $visibleBy)),
				'editableBy' => array_filter(array_map('trim', $editableBy)),
				'isHidden' => $input->visibility->alpha(),
				'errorMsg' => $input->error_message->text(),
				'permName' => $permName,
			));
		}

		return array(
			'field' => $field,
			'info' => $typeInfo,
			'options' => $this->parseOptions($field['options_array'], $typeInfo),
			'validation_types' => array(
				'' => tr('None'),
				'captcha' => tr('Captcha'),
				'distinct' => tr('Distinct'),
				'pagename' => tr('Page Name'),
				'password' => tr('Password'),
				'regex' => tr('Regular Expression (Pattern)'),
				'username' => tr('User Name'),
			),
		);
	}

	function action_remove_fields($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}
		
		$trackerId = $input->trackerId->int();
		$fields = $input->fields->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker not found'), 404);
		}

		foreach ($fields as $fieldId) {
			if (! $definition->getField($fieldId)) {
				throw new Services_Exception(tr('Field does not exist in tracker'), 404);
			}
		}

		if ($input->confirm->int()) {
			$trklib = TikiLib::lib('trk');
			foreach ($fields as $fieldId) {
				$trklib->remove_tracker_field($fieldId, $trackerId);
			}

			return array(
				'status' => 'DONE',
				'trackerId' => $trackerId,
				'fields' => $fields,
			);
		} else {
			return array(
				'trackerId' => $trackerId,
				'fields' => $fields,
			);
		}
	}

	function action_export_fields($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}
		
		$trackerId = $input->trackerId->int();
		$fields = $input->fields->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker not found'), 404);
		}

		if ($fields) {
			$fields = $this->getFieldsFromIds($definition, $fields);
		} else {
			$fields = $definition->getFields();
		}

		$data = "";
		foreach ($fields as $field) {
			$data .= $this->exportField($field);
		}

		return array(
			'trackerId' => $trackerId,
			'fields' => $fields,
			'export' => $data,
		);
	}

	function action_import_fields($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker not found'), 404);
		}
		
		$raw = $input->raw->none();
		$preserve = $input->preserve_ids->int();

		$data = TikiLib::lib('tiki')->read_raw($raw);

		if (! $data) {
			throw new Services_Exception(tr('Invalid data provided'), 400);
		}

		foreach ($data as $info) {
			$this->importField($trackerId, new JitFilter($info), $preserve);
		}

		return array(
			'trackerId' => $trackerId,
		);
	}

	function action_list_trackers($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trklib = TikiLib::lib('trk');
		return $trklib->list_trackers();
	}

	function action_clone_remote($input)
	{
		global $prefs;

		if ($prefs['tracker_remote_sync'] != 'y') {
			throw new Services_Exception_Disabled('tracker_remote_sync');
		}

		$url = $input->url->url();
		$remoteTracker = $input->remote_tracker_id->int();

		if ($url) {
			$serviceUrl = rtrim($url, '/') . '/tiki-ajax_services.php?';
			$tracker = $this->findTrackerInfo($serviceUrl, $remoteTracker);

			if (! $tracker) {
				// Prepare the list for tracker selection
				$trackers = $this->getRemoteTrackerList($serviceUrl);
				return array(
					'url' => $url,
					'list' => $trackers['list'],
				);
			} else {
				// Proceed with the tracker import
				$export = $this->getRemoteTrackerFieldExport($serviceUrl, $remoteTracker);

				$trackerId = $this->createTracker($tracker);
				$this->action_import_fields(new JitFilter(array(
					'trackerId' => 'trackerId',
					'raw' => $export,
				)));

				return array(
					'trackerId' => $trackerId,
				);
			}
		}

		return array(
			'url' => $url,
		);
	}

	private function importField($trackerId, $field, $preserve)
	{
		$fieldId = $field->fieldId->int();

		if (! $preserve) {
			$fieldId = 0;
		}

		$description = $field->descriptionStaticText->text();
		if (! $description) {
			$description = $field->description->text();
		}

		$data = array(
			'name' => $field->name->text(),
			'permName' => $field->permName->word(),
			'type' => $field->type->word(),
			'position' => $field->position->int(),
			'options' => $field->options->none(),

			'isMain' => $field->isMain->alpha(),
			'isSearchable' => $field->isSearchable->alpha(),
			'isTblVisible' => $field->isTblVisible->alpha(),
			'isPublic' => $field->isPublic->alpha(),
			'isHidden' => $field->isHidden->alpha(),
			'isMandatory' => $field->isMandatory->alpha(),
			'isMultilingual' => $field->isMultilingual->alpha(),

			'description' => $description,
			'descriptionIsParsed' => $field->descriptionIsParsed->alpha(),

			'validation' => $field->validation->word(),
			'validationParam' => $field->validationParam->none(),
			'validationMessage' => $field->validationMessage->text(),

			'itemChoices' => '',

			'editableBy' => $field->editableBy->groupname(),
			'visibleBy' => $field->visibleBy->groupname(),
			'errorMsg' => $field->errorMsg->text(),
		);

		$this->updateField($trackerId, $fieldId, $data);
	}

	private function exportField($field)
	{
		return <<<EXPORT
[FIELD{$field['fieldId']}]
fieldId = {$field['fieldId']}
name = {$field['name']}
permName = {$field['permName']}
position = {$field['position']}
type = {$field['type']}
options = {$field['options']}
isMain = {$field['isMain']}
isTblVisible = {$field['isTblVisible']}
isSearchable = {$field['isSearchable']}
isPublic = {$field['isPublic']}
isHidden = {$field['isHidden']}
isMandatory = {$field['isMandatory']}
description = {$field['description']}
descriptionIsParsed = {$field['descriptionIsParsed']}

EXPORT;
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
				// There is a possibility that * does not mean all of the remaining, to apply reasonable heuristic
				$filter = TikiFilter::get($info['filter']);
				$outarray = array(); 
				foreach ($raw as $r) {
					$filtered = $filter->filter($r);
					if (strcmp($filtered, $r) == 0) {
						$outarray[] = array_shift($raw); 
					} else {
						break;
					} 
				}
				$out[$key] = implode(',', $outarray);
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
			$data['descriptionIsParsed'] ? 'y' : 'n',
			'',
			'',
			'',
			$data['permName']
		);
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
			isset($properties['type']) ? $properties['type'] : $field['type'],
			isset($properties['isMain']) ? $properties['isMain'] : $field['isMain'],
			isset($properties['isSearchable']) ? $properties['isSearchable'] : $field['isSearchable'],
			isset($properties['isTblVisible']) ? $properties['isTblVisible'] : $field['isTblVisible'],
			isset($properties['isPublic']) ? $properties['isPublic'] : $field['isPublic'],
			isset($properties['isHidden']) ? $properties['isHidden'] : $field['isHidden'],
			isset($properties['isMandatory']) ? $properties['isMandatory'] : $field['isMandatory'],
			isset($properties['position']) ? $properties['position'] : $field['position'],
			isset($properties['options']) ? $properties['options'] : $field['options'],
			isset($properties['description']) ? $properties['description'] : $field['description'],
			isset($properties['isMultilingual']) ? $properties['isMultilingual'] : $field['isMultilingual'],
			'', // itemChoices
			isset($properties['errorMsg']) ? $properties['errorMsg'] : $field['errorMsg'],
			isset($properties['visibleBy']) ? $properties['visibleBy'] : $field['visibleBy'],
			isset($properties['editableBy']) ? $properties['editableBy'] : $field['editableBy'],
			isset($properties['descriptionIsParsed']) ? $properties['descriptionIsParsed'] : $field['descriptionIsParsed'],
			isset($properties['validation']) ? $properties['validation'] : $field['validation'],
			isset($properties['validationParam']) ? $properties['validationParam'] : $field['validationParam'],
			isset($properties['validationMessage']) ? $properties['validationMessage'] : $field['validationMessage'],
			isset($properties['permName']) ? $properties['permName'] : $field['permName']
		);
	}

	private function getFieldsFromIds($definition, $fieldIds)
	{
		$fields = array();
		foreach ($fieldIds as $fieldId) {
			$field = $field = $definition->getField($fieldId);

			if (! $field) {
				throw new Services_Exception(tr('Field does not exist in tracker'), 404);
			}

			$fields[] = $field;
		}

		return $fields;
	}

	private function createTracker($data)
	{
		return 0; // TODO
	}

	private function getRemoteTrackerList($serviceUrl)
	{
		static $cache = array();
		if (isset($cache[$serviceUrl])) {
			return $cache[$serviceUrl];
		}

		$tikilib = TikiLib::lib('tiki');
		$client = $tikilib->get_http_client($serviceUrl . http_build_query(array(
			'controller' => 'tracker',
			'action' => 'list_trackers',
		), '', '&'));
		$client->setHeaders('Accept', 'application/json');

		$response = $client->request();

		if (! $response->isSuccessful()) {
			throw new Services_Exception(tr('Remote service unaccessible (%0)', $response->getStatus()), 400);
		}

		return $cache[$serviceUrl] = json_decode($response->getBody(), true);
	}

	private function getRemoteTrackerFieldExport($serviceUrl, $trackerId)
	{
		$tikilib = TikiLib::lib('tiki');
		$client = $tikilib->get_http_client($serviceUrl . http_build_query(array(
			'controller' => 'tracker',
			'action' => 'export_fields',
			'trackerId' => $trackerId,
		), '', '&'));
		$client->setHeaders('Accept', 'application/json');
		$response = $client->request();

		if (! $response->isSuccessful()) {
			throw new Services_Exception(tr('Remote service unaccessible (%0)', $response->getStatus()), 400);
		}

		$export = json_decode($response->getBody(), true);
		return $export['export'];
	}

	private function findTrackerInfo($serviceUrl, $trackerId)
	{
		$trackers = $this->getRemoteTrackerList($serviceUrl);
		foreach ($trackers['data'] as $info) {
			if ($info['trackerId'] == $trackerId) {
				unset($info['trackerId']);
				return $info;
			}
		}
	}
}

