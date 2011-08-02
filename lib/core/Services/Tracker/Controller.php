<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_Controller
{
	private $utilities;

	function __construct()
	{
		$this->utilities = new Services_Tracker_Utilities;
	}

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

		$types = $this->utilities->getFieldTypes($description);

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

			$fieldId = $this->utilities->createField(array(
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
			'types' => $this->utilities->getFieldTypes(),
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

			$this->utilities->updateField($trackerId, $fieldId, $fields[$fieldId]);
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

		$types = $this->utilities->getFieldTypes($description);
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
			$this->utilities->updateField($trackerId, $fieldId, array(
				'name' => $input->name->text(),
				'description' => $input->description->text(),
				'descriptionIsParsed' => $input->description_parse->int() ? 'y' : 'n',
				'options' => $this->utilities->buildOptions($input->option, $typeInfo),
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
			'options' => $this->utilities->parseOptions($field['options_array'], $typeInfo),
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
			$fields = $this->utilities->getFieldsFromIds($definition, $fields);
		} else {
			$fields = $definition->getFields();
		}

		$data = "";
		foreach ($fields as $field) {
			$data .= $this->utilities->exportField($field);
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

		$factory = new Tracker_Field_Factory($definition);
		foreach ($data as $info) {
			$this->utilities->importField($trackerId, new JitFilter($info), $preserve);
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

		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$url = $input->url->url();
		$remoteTracker = $input->remote_tracker_id->int();

		if ($url) {
			$url = rtrim($url, '/');
			$tracker = $this->findTrackerInfo($url, $remoteTracker);

			if (! $tracker) {
				// Prepare the list for tracker selection
				$trackers = $this->getRemoteTrackerList($url);
				return array(
					'url' => $url,
					'list' => $trackers['list'],
				);
			} else {
				// Proceed with the tracker import
				$export = $this->getRemoteTrackerFieldExport($url, $remoteTracker);

				$trackerId = $this->utilities->createTracker($tracker);
				$this->createSynchronizedFields($trackerId, $export, array(
					'provider' => $url,
					'source' => $remoteTracker,
					'last' => 0,
				));
				$this->utilities->createField(array(
					'trackerId' => $trackerId,
					'type' => 't',
					'name' => tr('Remote Source'),
					'permName' => 'syncSource',
					'description' => tr('Automatically generated field for synchronized trackers. Contains the itemId of the remote item.'),
					'options' => $this->utilities->buildOptions(array(
						'prepend' => $url . '/item',
					), 't'),
					'isMandatory' => false,
				));

				$this->registerSynchronization($trackerId, $url, $remoteTracker);

				return array(
					'trackerId' => $trackerId,
				);
			}
		}

		return array(
			'url' => $url,
		);
	}

	function action_sync_refresh($input)
	{
		global $prefs;

		if ($prefs['tracker_remote_sync'] != 'y') {
			throw new Services_Exception_Disabled('tracker_remote_sync');
		}

		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trackerId = $input->trackerId->int();
		$confirm = $input->confirm->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $confirm) {
			throw new Services_Exception(tr('Missing input parameters'), 400);
		}

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$syncInfo = $definition->getSyncInformation();

		if (! $syncInfo) {
			throw new Services_Exception(tr('Tracker is not synchronized with a remote source.'), 409);
		}

		$this->utilities->clearTracker($trackerId);
		
		$itemMap = array();

		$remoteDefinition = $this->getRemoteDefinition($definition);
		$factory = new Tracker_Field_Factory($remoteDefinition);
		foreach ($this->getRemoteItems($syncInfo) as $item) {
			foreach ($item['fields'] as $key => & $value) {
				$field = $remoteDefinition->getFieldFromPermName($key);
				if ($field && $definition->getFieldFromPermName($key)) {
					$handler = $factory->getHandler($field);
					$value = $handler->import($value);
				}
			}

			$item['fields']['syncSource'] = $item['itemId'];
			$newItem = $this->utilities->insertItem($definition, $item);

			$itemMap[ $item['itemId'] ] = $newItem;
		}

		if ($definition->getLanguageField()) {
			$this->attachTranslations($syncInfo, 'trackeritem', $itemMap);
		}

		$this->registerSynchronization($trackerId, $syncInfo['provider'], $syncInfo['source']);
		return array();
	}

	function action_sync_new($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$syncInfo = $definition->getSyncInformation();

		if (! $syncInfo) {
			throw new Services_Exception(tr('Tracker is not synchronized with a remote source.'), 409);
		}

		$items = $input->items->int();
		
		$trklib = TikiLib::lib('trk');
		$syncField = $definition->getFieldFromPermName('syncSource');
		$itemIds = $trklib->get_items_list($trackerId, $syncField['fieldId'], '', 'opc');

		if ($items) {
			$itemIds = array_intersect($itemIds, $items);
			$table = TikiDb::get()->table('tiki_tracker_items');
			$items = $this->utilities->getItems(array(
				'trackerId' => $trackerId,
				'itemId' => $table->in($itemIds),
			));

			$remoteDefinition = $this->getRemoteDefinition($definition);
			foreach ($items as $item) {
				$remoteItemId = $this->insertRemoteItem($remoteDefinition, $definition, $syncInfo, $item);

				if ($remoteItemId) {
					$item['fields']['syncSource'] = $remoteItemId;
					$this->utilities->updateItem($definition, $item);
				}
			}

			return array(
			);
		} else {
			$out = array();
			foreach ($itemIds as $itemId) {
				$out[] = array(
					'itemId' => $itemId,
					'title' => $trklib->get_isMain_value(null, $itemId),
				);
			}

			return array(
				'trackerId' => $trackerId,
				'result' => $out,
			);
		}
	}

	function action_list_items($input)
	{
		// TODO : Eventually, this method should filter according to the actual permissions, but because
		//        it is only to be used for tracker sync at this time, admin privileges are just fine.

		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trackerId = $input->trackerId->int();
		$offset = $input->offset->int();
		$maxRecords = $input->maxRecords->int();
		
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$items = $this->utilities->getItems(array(
			'trackerId' => $trackerId,
		), $maxRecords, $offset);

		return array(
			'trackerId' => $trackerId,
			'offset' => $offset,
			'maxRecords' => $maxRecords,
			'result' => $items,
		);
	}

	function action_insert_item($input)
	{
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		// TODO : Eventually, this method should check the track permissions
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$itemId = $this->utilities->insertItem($definition, array(
			'status' => $input->status->word(),
			'fields' => $input->fields->none(),
		));

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
		);
	}

	private function createSynchronizedFields($trackerId, $data, $syncInfo)
	{
		if (! $data) {
			throw new Services_Exception(tr('Invalid data provided'), 400);
		}

		$factory = new Tracker_Field_Factory($definition);
		foreach ($data as $info) {
			$handler = $factory->getHandler($info);
			if ($handler instanceof Tracker_Field_Synchronizable) {
				$importable = $handler->importField($info, $syncInfo);
				$this->utilities->importField($trackerId, new JitFilter($importable), false);
			}
		}
	}

	private function getRemoteTrackerList($serviceUrl)
	{
		static $cache = array();
		if (isset($cache[$serviceUrl])) {
			return $cache[$serviceUrl];
		}

		$controller = new Services_RemoteController($serviceUrl, 'tracker');
		$data = $controller->list_trackers();
		return $cache[$serviceUrl] = $data;
	}

	private function getRemoteTrackerFieldExport($serviceUrl, $trackerId)
	{
		$controller = new Services_RemoteController($serviceUrl, 'tracker');
		$export = $controller->export_fields(array(
			'trackerId' => $trackerId,
		));

		return TikiLib::lib('tiki')->read_raw($export['export']);
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

	private function registerSynchronization($localTrackerId, $serviceUrl, $remoteTrackerId)
	{
		$attributelib = TikiLib::lib('attribute');
		$attributelib->set_attribute('tracker', $localTrackerId, 'tiki.sync.provider', rtrim($serviceUrl, '/'));
		$attributelib->set_attribute('tracker', $localTrackerId, 'tiki.sync.source', $remoteTrackerId);
		$attributelib->set_attribute('tracker', $localTrackerId, 'tiki.sync.last', time());
	}

	private function getRemoteItems($syncInfo)
	{
		$controller = new Services_RemoteController($syncInfo['provider'], 'tracker');
		return $controller->getResultLoader('list_items', array(
			'trackerId' => $syncInfo['source'],
		), 'offset', 'maxRecords', 'result');
	}

	private function insertRemoteItem($remoteDefinition, $definition, $syncInfo, $item)
	{
		unset($item['fields']['syncSource']);
		$item['trackerId'] = $syncInfo['source'];

		$factory = new Tracker_Field_Factory($definition);
		foreach ($item['fields'] as $key => & $value) {
			$field = $remoteDefinition->getFieldFromPermName($key);
			if ($field && $definition->getFieldFromPermName($key)) {
				$handler = $factory->getHandler($field);
				$value = $handler->export($value);
			}
		}

		$controller = new Services_RemoteController($syncInfo['provider'], 'tracker');
		$data = $controller->insert_item($item);
		
		if (isset($data['itemId']) && $data['itemId']) {
			return $data['itemId'];
		}
	}

	private function attachTranslations($syncInfo, $type, $objectMap)
	{
		$unprocessed = $objectMap;
		$utilities = new Services_Language_Utilities;

		while (reset($unprocessed)) {
			$remoteSource = key($unprocessed);
			
			unset($unprocessed[$remoteSource]);

			$translations = $this->getRemoteTranslations($syncInfo, $type, $remoteSource);
			foreach ($translations as $remoteTarget) {
				unset($unprocessed[$remoteTarget]);
				$utilities->insertTranslation($type, $objectMap[ $remoteSource ], $objectMap[ $remoteTarget ]);
			}
		}
	}

	private function getRemoteTranslations($syncInfo, $type, $remoteSource)
	{
		$controller = new Services_RemoteController($syncInfo['provider'], 'translation');
		$data = $controller->manage(array(
			'type' => $type,
			'source' => $remoteSource,
		));

		$out = array();

		if ($data['translations']) {
			foreach ($data['translations'] as $translation) {
				if ($translation['objId'] != $remoteSource) {
					$out[] = $translation['objId'];
				}
			}
		}

		return $out;
	}

	private function getRemoteDefinition($definition)
	{
		$syncInfo = $definition->getSyncInformation();

		return Tracker_Definition::createFake(
			$definition->getInformation(),
			$this->getRemoteTrackerFieldExport($syncInfo['provider'], $syncInfo['source'])
		);
	}
}

