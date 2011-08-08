<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_SyncController
{
	private $utilities;

	function setUp()
	{
		global $prefs;
		$this->utilities = new Services_Tracker_Utilities;

		if ($prefs['feature_trackers'] != 'y') {
			throw new Services_Exception_Disabled('feature_trackers');
		}

		if ($prefs['tracker_remote_sync'] != 'y') {
			throw new Services_Exception_Disabled('tracker_remote_sync');
		}

		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}
	}

	function action_clone_remote($input)
	{
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

	function action_sync_meta($input)
	{
		list($trackerId, $definition, $syncInfo) = $this->readTracker($input);
		$factory = new Tracker_Field_Factory($definition);

		$export = $this->getRemoteTrackerFieldExport($syncInfo['provider'], $syncInfo['source']);
		foreach ($export as $info) {
			$localField = $definition->getFieldFromPermName($info['permName']);
			if (! $localField) {
				continue;
			}

			$handler = $factory->getHandler($info);
			if (! $handler instanceof Tracker_Field_Synchronizable) {
				continue;
			}

			$importable = $handler->importField($info, $syncInfo);
			$this->utilities->updateField($trackerId, $localField['fieldId'], $importable);
		}

		return array();
	}

	function action_sync_refresh($input)
	{
		list($trackerId, $definition, $syncInfo) = $this->readTracker($input);

		set_time_limit(0); // Expected to take a while on larger trackers

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
		TikiLib::lib('unifiedsearch')->processUpdateQueue(count($itemMap) * 3); // Process lots of inserts
		return array();
	}

	function action_sync_new($input)
	{
		list($trackerId, $definition, $syncInfo) = $this->readTracker($input);

		$items = $input->items->int();
		
		$trklib = TikiLib::lib('trk');
		$syncField = $definition->getFieldFromPermName('syncSource');
		$itemIds = $trklib->get_items_list($trackerId, $syncField['fieldId'], '', 'opc');

		if ($items) {
			set_time_limit(30 + 10*count($items)); // 10 sec per item plus some initial overhead
			$itemIds = array_intersect($itemIds, $items);
			$table = TikiDb::get()->table('tiki_tracker_items');
			$items = $this->utilities->getItems(array(
				'trackerId' => $trackerId,
				'itemId' => $table->in($itemIds),
			));

			$remoteDefinition = $this->getRemoteDefinition($definition);
			foreach ($items as $item) {
				$remoteItemId = $this->insertRemoteItem($remoteDefinition, $definition, $item);

				if ($remoteItemId) {
					$item['fields']['syncSource'] = $remoteItemId;
					$this->utilities->updateItem($definition, $item);
				}
			}
			TikiLib::lib('unifiedsearch')->processUpdateQueue();

			return array(
			);
		} else {
			return array(
				'trackerId' => $trackerId,
				'sets' => array('items'),
				'items' => $this->getItemList($itemIds),
			);
		}
	}

	function action_sync_edit($input)
	{
		list($trackerId, $definition, $syncInfo) = $this->readTracker($input);

		// Collect local IDs that were modified
		$items = TikiDb::get()->table('tiki_tracker_items');
		$itemIds = $items->fetchColumn('itemId', array(
			'trackerId' => $trackerId,
			'created' => $items->lesserThan($syncInfo['last']),
			'lastModif' => $items->greaterThan($syncInfo['last']),
		));

		// Collect remote IDs that were modified
		$remoteItems = $this->getRemoteItems($syncInfo, array(
			'modifiedSince' => $syncInfo['last'],
		));

		$modifiedIds = array();
		foreach ($remoteItems as $item) {
			$modifiedIds[] = $item['itemId'];
		}

		// Map from remote ID to local ID
		$syncField = $definition->getFieldFromPermName('syncSource');
		$fields = TikiDb::get()->table('tiki_tracker_item_fields');
		$modifiedIds = $fields->fetchColumn('itemId', array(
			'fieldId' => $syncField['fieldId'],
			'value' => $fields->in($modifiedIds),
		));

		$automatic = array_diff($itemIds, $modifiedIds);
		$manual = array_intersect($itemIds, $modifiedIds);

		set_time_limit(30 + 10*count($automatic) + 10*count($manual)); // 10 sec per item plus some initial overhead

		$remoteDefinition = $this->getRemoteDefinition($definition);
		$this->processUpdates('automatic', $automatic, $input, $definition, $remoteDefinition);
		$this->processUpdates('manual', $manual, $input, $definition, $remoteDefinition);

		return array(
			'trackerId' => $trackerId,
			'sets' => array('automatic', 'manual'),
			'automatic' => $this->getItemList($automatic),
			'manual' => $this->getItemList($manual),
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
		$attributelib->set_attribute('tracker', $localTrackerId, 'tiki.sync.last', time()); // Real sync time, not tiki initial load
	}

	private function getRemoteItems($syncInfo, array $conditions = array())
	{
		$controller = new Services_RemoteController($syncInfo['provider'], 'tracker');
		return $controller->getResultLoader('list_items', array_merge($conditions, array(
			'trackerId' => $syncInfo['source'],
			'format' => 'raw',
		)), 'offset', 'maxRecords', 'result');
	}

	private function insertRemoteItem($remoteDefinition, $definition, $item)
	{
		$syncInfo = $definition->getSyncInformation();

		$item['trackerId'] = $syncInfo['source'];
		$item['fields'] = $this->exportFields($item['fields'], $remoteDefinition, $definition);

		$controller = new Services_RemoteController($syncInfo['provider'], 'tracker');
		$data = $controller->insert_item($item);
		
		if (isset($data['itemId']) && $data['itemId']) {
			return $data['itemId'];
		}
	}

	private function updateRemoteItem($remoteDefinition, $definition, $item)
	{
		$syncInfo = $definition->getSyncInformation();

		$item['itemId'] = $item['fields']['syncSource'];
		$item['trackerId'] = $syncInfo['source'];

		$item['fields'] = $this->exportFields($item['fields'], $remoteDefinition, $definition);

		$controller = new Services_RemoteController($syncInfo['provider'], 'tracker');
		$controller->update_item($item);
	}

	private function exportFields($fields, $remoteDefinition, $definition)
	{
		unset($fields['syncSource']);
		$factory = new Tracker_Field_Factory($definition);
		foreach ($fields as $key => & $value) {
			$field = $remoteDefinition->getFieldFromPermName($key);
			if ($field && $definition->getFieldFromPermName($key)) {
				$handler = $factory->getHandler($field);
				$value = $handler->export($value);
			}
		}
		
		return $fields;
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

	private function readTracker($input)
	{
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$syncInfo = $definition->getSyncInformation();

		if (! $syncInfo) {
			throw new Services_Exception(tr('Tracker is not synchronized with a remote source.'), 409);
		}

		return array($trackerId, $definition, $syncInfo);
	}
	
	private function getItemList($itemIds)
	{
		$trklib = TikiLib::lib('trk');

		$out = array();
		foreach ($itemIds as $itemId) {
			$out[] = array(
				'itemId' => $itemId,
				'title' => $trklib->get_isMain_value(null, $itemId),
			);
		}

		return $out;
	}
	
	private function processUpdates($inputType, & $list, $input, $definition, $remoteDefinition)
	{
		$values = $input->$inputType->int();
		if (! is_array($values)) {
			return;
		}

		$toProcess = array_intersect($list, $values);
		$list = array_diff($list, $values);

		$table = TikiDb::get()->table('tiki_tracker_items');
		$itemList = $this->utilities->getItems(array(
			'trackerId' => $definition->getConfiguration('trackerId'),
			'itemId' => $table->in($toProcess),
		));
		foreach ($itemList as $item) {
			$this->updateRemoteItem($remoteDefinition, $definition, $item);
		}
	}
}

