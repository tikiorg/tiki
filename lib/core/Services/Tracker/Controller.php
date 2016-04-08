<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_Controller
{
	/**
	 * @var Services_Tracker_Utilities
	 */
	private $utilities;

	function setUp()
	{
		global $prefs;
		$this->utilities = new Services_Tracker_Utilities;

		Services_Exception_Disabled::check('feature_trackers');
	}

	function action_view($input)
	{
		$item = Tracker_Item::fromId($input->id->int());
			
		if (! $item) {
			throw new Services_Exception_NotFound('Item not found');
		}

		if (! $item->canView()) {
			throw new Services_Exception_Denied('Permission denied');
		}

		$definition = $item->getDefinition();

		$fields = $item->prepareOutput(new JitFilter([]));

		return [
			'title' => TikiLib::lib('object')->get_title('trackeritem', $item->getId()),
			'format' => $input->format->word(),
			'itemId' => $item->getId(),
			'trackerId' => $definition->getConfiguration('trackerId'),
			'fields' => $fields,
			'canModify' => $item->canModify(),
		];
	}

	function action_add_field($input)
	{
		$modal = $input->modal->int();
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$trklib = TikiLib::lib('trk');
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$name = $input->name->text();
		$permName = $input->permName->word();
		$type = $input->type->text();
		$description = $input->description->text();
		$wikiparse = $input->description_parse->int();
		$adminOnly = $input->adminOnly->int();
		$fieldId = 0;

		$types = $this->utilities->getFieldTypes();

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

			$fieldId = $this->utilities->createField(
				array(
					'trackerId' => $trackerId,
					'name' => $name,
					'permName' => $permName,
					'type' => $type,
					'description' => $description,
					'descriptionIsParsed' => $wikiparse,
					'isHidden' => $adminOnly ? 'y' : 'n',
				)
			);

			if ($input->submit_and_edit->none() || $input->next->word() === 'edit') {
				return array(
					'FORWARD' => array(
						'action' => 'edit_field',
						'fieldId' => $fieldId,
						'trackerId' => $trackerId,
						'modal' => $modal,
					),
				);
			}
		}

		return array(
			'title' => tr('Add Field'),
			'trackerId' => $trackerId,
			'fieldId' => $fieldId,
			'name' => $name,
			'permName' => $permName,
			'type' => $type,
			'types' => $types,
			'description' => $description,
			'descriptionIsParsed' => $wikiparse,
			'modal' => $modal,
		);
	}

	function action_list_fields($input)
	{
		global $prefs;

		$trackerId = $input->trackerId->int();
		$perms = Perms::get('tracker', $trackerId);

		if (! $perms->view_trackers) {
			throw new Services_Exception_Denied(tr("You don't have permission to view the tracker"));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$fields = $definition->getFields();
		$types = $this->utilities->getFieldTypes();
		$typesDisabled = array();

		if ($perms->admin_trackers) {
			$typesDisabled = $this->utilities->getFieldTypesDisabled();
		}

		$missing = array();
		$duplicates = array();

		foreach ($fields as $field) {
			if (! array_key_exists($field['type'], $types) && ! in_array($field['type'], $missing)) {
				$missing[] = $field['type'];
			}
			if ($prefs['unified_engine'] === 'elastic') {
				$tracker_fields = TikiLib::lib('tiki')->table('tiki_tracker_fields');
				$dupeFields = $tracker_fields->fetchAll(
					array(
						'fieldId',
						'trackerId',
						'name',
						'permName',
						'type',
					),
					array(
						'fieldId'  => $tracker_fields->not($field['fieldId']),
						'type'     => $tracker_fields->not($field['type']),
						'permName' => $field['permName'],
					)
				);
				if ($dupeFields) {
					foreach($dupeFields as & $df) {
						$df['message'] = tr('Warning: There is a conflict in permanent names, which can cause indexing errors.') .
							'<br><a href="tiki-admin_tracker_fields.php?trackerId=' . $df['trackerId'] . '">' .
							tr('Field #%0 "%1" of type "%2" also found in tracker #%3 with perm name %4',
								$df['fieldId'], $df['name'], $types[$df['type']]['name'], $df['trackerId'], $df['permName']) .
							'</a>';
					}
					$duplicates[$field['fieldId']] = $dupeFields;
				}
			}
		}
		if (!empty($missing)) {
			TikiLib::lib('errorreport')->report(tr('Warning: Required field types not enabled: %0', implode(', ', $missing)));
		}

		return array(
			'fields' => $fields,
			'types' => $types,
			'typesDisabled' => $typesDisabled,
			'duplicates' => $duplicates,
		);
	}

	function action_save_fields($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$hasList = false;
		$hasLink = false;

		$tx = TikiDb::get()->begin();

		$fields = array();
		foreach ($input->field as $key => $value) {
			$fieldId = (int) $key;
			$isMain = $value->isMain->int();
			$isTblVisible = $value->isTblVisible->int();

			$fields[$fieldId] = array(
				'position' => $value->position->int(),
				'isTblVisible' => $isTblVisible ? 'y' : 'n',
				'isMain' => $isMain ? 'y' : 'n',
				'isSearchable' => $value->isSearchable->int() ? 'y' : 'n',
				'isPublic' => $value->isPublic->int() ? 'y' : 'n',
				'isMandatory' => $value->isMandatory->int() ? 'y' : 'n',
			);

			$this->utilities->updateField($trackerId, $fieldId, $fields[$fieldId]);

			$hasList = $hasList || $isTblVisible;
			$hasLink = $hasLink || $isMain;
		}

		$errorreport = TikiLib::lib('errorreport');
		if (! $hasList) {
			$errorreport->report(tr('Tracker contains no listed field, no meaningful information will be provided in the default list.'));
		}

		if (! $hasLink) {
			$errorreport->report(tr('The tracker contains no field in the title, so no link will be generated.'));
		}

		$errorreport->send_headers();
		$tx->commit();

		return array(
			'fields' => $fields,
		);
	}

	function action_edit_field($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$fieldId = $input->fieldId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$field = $definition->getField($fieldId);
		if (! $field) {
			throw new Services_Exception_NotFound;
		}

		$types = $this->utilities->getFieldTypes();
		$typeInfo = $types[$field['type']];

		$permName = $input->permName->word();
		if ($field['permName'] != $permName) {
			if ($definition->getFieldFromPermName($permName)) {
				throw new Services_Exception_DuplicateValue('permName', $permName);
			}
		}

		if ($input->name->text()) {
			$input->replaceFilters(
				array(
					'visible_by' => 'groupname',
					'editable_by' => 'groupname',
				)
			);
			$visibleBy = $input->asArray('visible_by', ',');
			$editableBy = $input->asArray('editable_by', ',');

			global $prefs;
			if ($prefs['tracker_change_field_type'] === 'y') {
				$type = $input->type->text();
				if ($field['type'] !== $type) {
					if (!isset($types[$type])) {
						throw new Services_Exception(tr('Type does not exist'), 400);
					}
					$typeInfo = $types[$type]; // update typeInfo and clear out old options if changed type
					$input->offsetSet('option', new JitFilter(array()));
				}
			} else {
				$type = $field['type'];
			}

			$this->utilities->updateField(
				$trackerId,
				$fieldId,
				array(
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
					'type' => $type,
				)
			);
		}

		array_walk($typeInfo['params'], function (& $param) {
			if (isset($param['profile_reference'])) {
				$lib = TikiLib::lib('object');
				$param['selector_type'] = $lib->getSelectorType($param['profile_reference']);
				$param['parent'] = isset($param['parent']) ? "#option-{$param['parent']}" : null;
				$param['parentkey'] = isset($param['parentkey']) ? $param['parentkey'] : null;
			} else {
				$param['selector_type'] = null;
			}
		});

		return array(
			'title' => tr('Edit %0', $field['name']),
			'field' => $field,
			'info' => $typeInfo,
			'options' => $this->utilities->parseOptions($field['options'], $typeInfo),
			'validation_types' => array(
				'' => tr('None'),
				'captcha' => tr('CAPTCHA'),
				'distinct' => tr('Distinct'),
				'pagename' => tr('Page Name'),
				'password' => tr('Password'),
				'regex' => tr('Regular Expression (Pattern)'),
				'username' => tr('Username'),
			),
			'types' => $types,
		);
	}

	function action_remove_fields($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$fields = $input->fields->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		foreach ($fields as $fieldId) {
			if (! $definition->getField($fieldId)) {
				throw new Services_Exception_NotFound;
			}
		}

		if ($input->confirm->int()) {
			$trklib = TikiLib::lib('trk');
			$tx = TikiDb::get()->begin();
			foreach ($fields as $fieldId) {
				$trklib->remove_tracker_field($fieldId, $trackerId);
			}
			$tx->commit();

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
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$fields = $input->fields->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
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
			'title' => tr('Export Fields'),
			'trackerId' => $trackerId,
			'fields' => $fields,
			'export' => $data,
		);
	}

	function action_import_fields($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$raw = $input->raw->none();
		$preserve = $input->preserve_ids->int();

		$data = TikiLib::lib('tiki')->read_raw($raw);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (! $data) {
				throw new Services_Exception(tr('Invalid data provided'), 400);
			}

			foreach ($data as $info) {
				$this->utilities->importField($trackerId, new JitFilter($info), $preserve);
			}
		}

		return array(
			'title' => tr('Import Tracker Fields'),
			'trackerId' => $trackerId,
		);
	}

	function action_list_trackers($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$trklib = TikiLib::lib('trk');
		return $trklib->list_trackers();
	}

	function action_list_items($input)
	{
		// TODO : Eventually, this method should filter according to the actual permissions, but because
		//        it is only to be used for tracker sync at this time, admin privileges are just fine.

		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$trackerId = $input->trackerId->int();
		$offset = $input->offset->int();
		$maxRecords = $input->maxRecords->int();
		$status = $input->status->word();
		$format = $input->format->word();
		$modifiedSince = $input->modifiedSince->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$items = $this->utilities->getItems(
			array(
				'trackerId' => $trackerId,
				'status' => $status,
				'modifiedSince' => $modifiedSince,
			),
			$maxRecords,
			$offset
		);

		if ($format !== 'raw') {
			foreach ($items as & $item) {
				$item = $this->utilities->processValues($definition, $item);
			}
		}

		return array(
			'trackerId' => $trackerId,
			'offset' => $offset,
			'maxRecords' => $maxRecords,
			'result' => $items,
		);
	}

	function action_get_item_inputs($input)
	{
		$trackerId = $input->trackerId->int();
		$trackerName = $input->trackerName->text();
		$itemId = $input->itemId->int();
		$byName = $input->byName->bool();
		$defaults = $input->defaults->array();

		$this->trackerNameAndId($trackerId, $trackerName);

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::newItem($trackerId);

		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$query = Tracker_Query::tracker($byName ? $trackerName : $trackerId)
			->itemId($itemId);

		if ($input > 0) $query->byName();
		if (!empty($defaults)) $query->inputDefaults($defaults);

		$inputs = $query
			->queryInput();

		return $inputs;
	}

	function action_clone_item($input)
	{
		global $prefs;

		Services_Exception_Disabled::check('tracker_clone_item');

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemId = $input->itemId->int();
		if (! $itemId ) {
			throw new Services_Exception_Denied(tr('No item to clone'));
		}

		$itemObject = Tracker_Item::fromId($itemId);

		if (! $itemObject->canView()) {
			throw new Services_Exception_Denied(tr("The item to clone isn't visible"));
		}

		$newItem = Tracker_Item::newItem($trackerId);

		if (! $newItem->canModify()) {
			throw new Services_Exception_Denied(tr("You don't have permission to create new items"));
		}

		global $prefs;
		if ($prefs['feature_jquery_validation'] === 'y') {
			$_REQUEST['itemId'] = 0;	// let the validation code know this will be a new item
			$validationjs = TikiLib::lib('validators')->generateTrackerValidateJS($definition->getFields());
			TikiLib::lib('header')->add_jq_onready('$("#cloneItemForm' . $trackerId . '").validate({' . $validationjs . $this->get_validation_options());
		}

		$itemObject->asNew();
		$itemData = $itemObject->getData($input);

		$id = 0;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$transaction = TikiLib::lib('tiki')->begin();

			$id = $this->utilities->insertItem($definition, $itemData);

			foreach ($definition->getFields() as $field) {
				$handler = $definition->getFieldFactory()->getHandler($field, $itemData);
				if (method_exists($handler, 'handleClone')) {
					$handler->handleClone();
				}
			}

			$itemObject = Tracker_Item::fromId($id);

			foreach (TikiLib::lib('trk')->get_child_items($itemId) as $info) {
				$childItem = Tracker_Item::fromId($info['itemId']);

				if ($childItem->canView()) {
					$childItem->asNew();
					$data = $childItem->getData();
					$data['fields'][$info['field']] = $id;

					$childDefinition = $childItem->getDefinition();

					// handle specific cloning actions

					foreach ($childDefinition->getFields() as $field) {
						$handler = $childDefinition->getFieldFactory()->getHandler($field, $data);
						if (method_exists($handler, 'handleClone')) {
							$newData = $handler->handleClone();
							$data['fields'][$field['permName']] = $newData['value'];
						}
					}

					$new = $this->utilities->insertItem($childDefinition, $data);

				}
			}

			$transaction->commit();
		}

		return array(
			'title' => tr('Duplicate Item'),
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'created' => $id,
			'data' => $itemData['fields'],
			'fields' => $itemObject->prepareInput(new JitFilter(array())),
		);
	}

	function action_insert_item($input)
	{
		$processedFields = array();

		$trackerId = $input->trackerId->int();

		if (! $trackerId) {
			return [
				'FORWARD' => ['controller' => 'tracker', 'action' => 'select_tracker'],
			];
		}

		$trackerName = $this->trackerName($trackerId);
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::newItem($trackerId);

		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$fields = $input->fields->none();
		$forced = $input->forced->none();

		if (empty($fields)) {
			$toRemove = array();
			$processedFields = $itemObject->prepareInput($input);

			$fields = array();
			foreach ($processedFields as $k => $f) {
				$permName = $f['permName'];
				$fields[$permName] = $f['value'];

				if (isset($forced[$permName])) {
					$toRemove[$permName] = $k;
				}
			}

			foreach ($toRemove as $permName => $key) {
				unset($fields[$permName]);
				unset($processedFields[$key]);
			}
		} else {
			$out = array();
			foreach ($fields as $key => $value) {
				if ($itemObject->canModifyField($key)) {
					$out[$key] = $value;
				}
			}
			$fields = $out;
		}

		global $prefs;
		if ($prefs['feature_jquery_validation'] === 'y') {
			$validationjs = TikiLib::lib('validators')->generateTrackerValidateJS($definition->getFields());
			TikiLib::lib('header')->add_jq_onready('$("#insertItemForm' . $trackerId . '").validate({' . $validationjs . $this->get_validation_options());
		}

		$itemId = 0;
		if (! empty($fields) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			foreach ($forced as $key => $value) {
				if ($itemObject->canModifyField($key)) {
					$fields[$key] = $value;
				}
			}

			// test if one item per user
			if ($definition->getConfiguration('oneUserItem', 'n') == 'y') {
				$tmp = TikiLib::lib('trk')->get_user_item($trackerId, $definition->getInformation());
				if ($tmp > 0) {
					throw new Services_Exception(tr('Item could not be created. Only one item per user is allowed.'), 400);
				}
			}

			$itemId = $this->utilities->insertItem(
				$definition,
				array(
					'status' => $input->status->word(),
					'fields' => $fields,
				)
			);

			if ($itemId) {
				TikiLib::lib('unifiedsearch')->processUpdateQueue();
				TikiLib::events()->trigger('tiki.process.redirect'); // wait for indexing to complete before loading of next request to ensure updated info shown
			
				if ($next = $input->next->url()) {
					$access = TikiLib::lib('access');
					$access->redirect($next, tr('Item created'));
				}

				$item = $this->utilities->getItem($trackerId, $itemId);
				$item['itemTitle'] = $this->utilities->getTitle($definition, $item);

				return $item;
			} else {
				throw new Services_Exception(tr('Item could not be created.'), 400);
			}
		}

		return array(
			'title' => tr('Create Item'),
			'trackerId' => $trackerId,
			'trackerName' => $trackerName,
			'itemId' => $itemId,
			'fields' => $processedFields,
			'forced' => $forced,
			'trackerLogo' => $definition->getConfiguration('logo'),
			'modal' => $input->modal->int(),
			'status' => $itemObject->getDisplayedStatus(),
			'format' => $input->format->word(),
		);
	}

	/**
	 * @param $input JitFilter
	 * - "trackerId" required
	 * - "itemId" required
	 * - "editable" optional. array of field names. e.g. ['title', 'description', 'user']. If not set, all fields
	 *    all fields will be editable
	 * - "forced" optional. associative array of fields where the value is 'forced'. Commonly used with skip_form.
	 *    e.g ['isArchived'=>'y']. For example, this can be used to create a button that allows you to set the
	 *    trackeritem to "Closed", or to set a field to a pre-determined value.
	 * - "skip_form" - Allows users to skip the input form. This must be used with "forced" or "status" otherwise nothing would change
	 * - "status" - sets a status for the object to be set to. Often used with skip_form
	 *
	 * Formatting the edit screen
	 * - "title" optional. Sets a title for the edit screen.
	 * - "skip_form_confirm_message" optional. Used with skip_form. E.g. "Are you sure you want to set this item to 'Closed'".
	 * - "button_label" optional. Used to override the label for the Update/Save button.
	 * - "redirect" set a url to which a user should be redirected, if any.
	 *
	 * @return array
	 * @throws Exception
	 * @throws Services_Exception
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_MissingValue
	 * @throws Services_Exception_NotFound
	 *
	 */
	function action_update_item($input)
	{
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		global $prefs;
		if ($prefs['feature_jquery_validation'] === 'y') {
			$validationjs = TikiLib::lib('validators')->generateTrackerValidateJS($definition->getFields());
			TikiLib::lib('header')->add_jq_onready('$("#updateItemForm' . $trackerId . '").validate({' . $validationjs . $this->get_validation_options());
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//fetch the processed fields and the changes made in the form. Put them in the 'fields' variable
			$processedFields = $itemObject->prepareInput($input);
			$fields = array();
			foreach ($processedFields as $k => $f) {
				$permName = $f['permName'];
				$fields[$permName] = isset($f['value']) ? $f['value'] : '';
			}
			// for each input from the form, ensure user has modify rights. If so, add to the fields var to be edited.
			$userInput = $input->fields->none();
			if (! empty($userInput)) {
				foreach ($userInput as $key => $value) {
					if ($itemObject->canModifyField($key)) {
						$fields[$key] = $value;
					}
				}
			}
			// for each input from the form, ensure user has modify rights. If so, add to the fields var to be edited.
			$forcedInput = $input->forced->none();
			if (! empty($forcedInput)) {
				foreach ($forcedInput as $key => $value) {
					if ($itemObject->canModifyField($key)) {
						$fields[$key] = $value;
					}
				}
			}

			$result = $this->utilities->updateItem(
				$definition,
				array(
					'itemId' => $itemId,
					'status' => $input->status->word(),
					'fields' => $fields,
				)
			);

			if (false === $result) {
				throw new Services_Exception(tr('Validation error'), 406);
			}

			TikiLib::lib('unifiedsearch')->processUpdateQueue();
			TikiLib::events()->trigger('tiki.process.redirect'); // wait for indexing to complete before loading of next request to ensure updated info shown
			return array(
				'modal' => '1',
				'url' => empty($input->redirect->none()) ? "" : $input->redirect->none(),
				'FORWARD' => array(
					'controller' => 'utilities',
					'action' => 'modal_alert',
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxmsg' => 'Your item has been updated.',
					'ajaxdismissible' => 'n',
					'ajaxtimer' => 5,
				)
			);
		}

		// sets all fields for the tracker item with their value
		$processedFields = $itemObject->prepareInput($input);
		// fields that we want to change in the form. If
		$editableFields = $input->editable->none();
		// fields where the value is forced.
		$forcedFields = $input->forced->none();

		// if forced fields are set, remove them from the processedFields since they will not show up visually
		// in the form; they will be set up separately and hidden.
		if (!empty($forcedFields)) {
			foreach ($processedFields as $k => $f) {
				$permName = $f['permName'];
				if (isset($forcedFields[$permName])) {
					unset($processedFields[$k]);
				}
			}
		}

		if (empty($editableFields)) {
			//if editable fields, show all fields in the form (except the ones from forced which have been removed).
			$displayedFields = $processedFields;
		} else {
			// if editableFields is set, only add the field if found in the editableFields array
			$displayedFields = array();
			foreach ($processedFields as $k => $f) {
				$permName = $f['permName'];
				if (in_array($permName, $editableFields)) {
					$displayedFields[] = $f;
				}
			}
		}

		/* Allow overriding of default wording in the template */
		if (empty($input->title->text())) {
			$title = tr('Update');
		} else {
			$title = $input->title->text();
		}

		//Used if skip form is set
		if (empty($input->skip_form_message->text())) {
			$skip_form_message = tr('Are you sure you would like to update this item?');
		} else {
			$skip_form_message = $input->skip_form_message->text();
		}

		if (empty($input->button_label->text())) {
			$button_label = tr('Save');
		} else {
			$button_label = $input->button_label->text();
		}

		if (empty($input->skip_form->word())) {
			$status = $itemObject->getDisplayedStatus();
		} else {
			$status = $input->status->word();
		}

		return array(
			'title' => $title,
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'fields' => $displayedFields,
			'forced' => $forcedFields,
			'status' => $status,
			'skip_form' => $input->skip_form->word(),
			'skip_form_message' => $skip_form_message,
			'format' => $input->format->word(),
			'button_label' => $button_label,
			'redirect' => $input->redirect->none(),
		);
	}

	function action_fetch_item_field($input)
	{
		$trackerId = $input->trackerId->int();
		$mode = $input->mode->word();						// output|input (default input)
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $field = $definition->getField($input->fieldId->int())) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $processed = $itemObject->prepareFieldInput($field, $input->none())) {
			throw new Services_Exception_Denied;
		}

		return array(
			'field' => $processed,
			'mode' => $mode,
		);
	}

	function action_set_location($input)
	{
		$location = $input->location->text();

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo) {
			throw new Services_Exception_NotFound;
		}

		$trackerId = $itemInfo['trackerId'];
		$definition = Tracker_Definition::get($trackerId);
		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$field = $definition->getGeolocationField();
		if (! $field) {
			throw new Services_Exception_NotFound;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$field = $definition->getField($field);

			$this->utilities->updateItem(
				$definition,
				array(
					'itemId' => $itemId,
					'status' => $itemInfo['status'],
					'fields' => array(
						$field['permName'] => $location,
					),
				)
			);
			TikiLib::lib('unifiedsearch')->processUpdateQueue();
			TikiLib::events()->trigger('tiki.process.redirect'); // wait for indexing to complete before loading of next request to ensure updated info shown
		}

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'location' => $location,
		);
	}

	function action_remove_item($input)
	{
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$trklib = TikiLib::lib('trk');

		$itemInfo = $trklib->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canRemove()) {
			throw new Services_Exception_Denied;
		}

		$uncascaded = $trklib->findUncascadedDeletes($itemId, $trackerId);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$tx = TikiDb::get()->begin();

			$itemData = $itemObject->getData();
			foreach ($definition->getFields() as $field) {
				$handler = $definition->getFieldFactory()->getHandler($field, $itemData);
				if (method_exists($handler, 'handleDelete')) {
					$handler->handleDelete();
				}
			}

			$trklib->replaceItemReferences($input->replacement->int() ?: '', $uncascaded['itemIds'], $uncascaded['fieldIds']);

			$this->utilities->removeItem($itemId);

			$tx->commit();

			TikiLib::events()->trigger('tiki.process.redirect'); // wait for indexing to complete before loading of next request to ensure updated info shown
		}

		return array(
			'title' => tr('Remove'),
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'affectedCount' => count($uncascaded['itemIds']),
		);
	}

	function action_remove($input)
	{
		$trackerId = $input->trackerId->int();
		$confirm = $input->confirm->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if ($confirm) {
			$this->utilities->removeTracker($trackerId);

			return array(
				'trackerId' => 0,
			);
		}

		return array(
			'trackerId' => $trackerId,
			'name' => $definition->getConfiguration('name'),
		);
	}

	//Function to just change the status of the tracker item
	function action_update_item_status($input)
	{
		if ($input->status->word() == 'DONE') {
			return array(
				'status' => 'DONE',
				'redirect' => $input->redirect->word(),
			);
		}

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		if (empty($input->item_label->text())){
			$item_label = "item";
		}else{
			$item_label = $input->item_label->text();
		}

		if (empty($input->title->text())){
			$title = "Change item status";
		}else{
			$title = $input->title->text();
		}

		if (empty($input->button_label->text())){
			$button_label = "Update ". $item_label;
		}else{
			$button_label = $input->button_label->text();
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		if ($input->confirm->int()) {
			$result = $this->utilities->updateItem(
				$definition,
				array(
					'itemId' => $itemId,
					'trackerId' => $trackerId,
					'status' => $input->status->text(),
				)
			);

			return array(
				'FORWARD' => array(
					'controller' => 'tracker',
					'action' => 'update_item_status',
					'status' => 'DONE',
					'redirect' => $input->redirect->text(),
				)
			);
		} else {
			return array(
				'trackerId' => $trackerId,
				'itemId' => $itemId,
				'item_label' => $item_label,
				'status' => $input->status->text(),
				'redirect' => $input->redirect->text(),
				'confirmation_message' => $input->confirmation_message->text(),
				'title' => $title,
				'button_label' => $button_label,
			);
		}
		if (false === $result) {
			throw new Services_Exception(tr('Validation error'), 406);
		}
	}

	function action_clear($input)
	{

		return TikiLib::lib('tiki')->allocate_extra(
			'tracker_clear_items',
			function () use ($input) {
				$trackerId = $input->trackerId->int();
				$confirm = $input->confirm->int();

				$perms = Perms::get('tracker', $trackerId);
				if (! $perms->admin_trackers) {
					throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
				}

				$definition = Tracker_Definition::get($trackerId);

				if (! $definition) {
					throw new Services_Exception_NotFound;
				}

				if ($confirm) {
					$this->utilities->clearTracker($trackerId);

					return array(
						'trackerId' => 0,
					);
				}

				return array(
					'trackerId' => $trackerId,
					'name' => $definition->getConfiguration('name'),
				);
			}
		);
	}

	function action_replace($input)
	{
		$trackerId = $input->trackerId->int();
		$confirm = $input->confirm->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		if ($trackerId) {
			$definition = Tracker_Definition::get($trackerId);

			if (! $definition) {
				throw new Services_Exception_NotFound;
			}
		} else {
			$definition = Tracker_Definition::getDefault();
		}

		$cat_type = 'tracker';
		$cat_objid = $trackerId;

		if ($confirm) {
			$name = $input->name->text();

			if (! $name) {
				throw new Services_Exception_MissingValue('name');
			}

			$data = array(
				'name' => $name,
				'description' => $input->description->text(),
				'descriptionIsParsed' => $input->descriptionIsParsed->int() ? 'y' : 'n',
				'showStatus' => $input->showStatus->int() ? 'y' : 'n',
				'showStatusAdminOnly' => $input->showStatusAdminOnly->int() ? 'y' : 'n',
				'showCreated' => $input->showCreated->int() ? 'y' : 'n',
				'showCreatedView' => $input->showCreatedView->int() ? 'y' : 'n',
				'showCreatedBy' => $input->showCreatedBy->int() ? 'y' : 'n',
				'showCreatedFormat' => $input->showCreatedFormat->text(),
				'showLastModif' => $input->showLastModif->int() ? 'y' : 'n',
				'showLastModifView' => $input->showLastModifView->int() ? 'y' : 'n',
				'showLastModifBy' => $input->showLastModifBy->int() ? 'y' : 'n',
				'showLastModifFormat' => $input->showLastModifFormat->text(),
				'defaultOrderKey' => $input->defaultOrderKey->int(),
				'defaultOrderDir' => $input->defaultOrderDir->word(),
				'doNotShowEmptyField' => $input->doNotShowEmptyField->int() ? 'y' : 'n',
				'showPopup' => $input->showPopup->text(),
				'defaultStatus' => implode('', (array) $input->defaultStatus->word()),
				'newItemStatus' => $input->newItemStatus->word(),
				'modItemStatus' => $input->modItemStatus->word(),
				'outboundEmail' => $input->outboundEmail->email(),
				'simpleEmail' => $input->simpleEmail->int() ? 'y' : 'n',
				'userCanSeeOwn' => $input->userCanSeeOwn->int() ? 'y' : 'n',
				'writerCanModify' => $input->writerCanModify->int() ? 'y' : 'n',
				'writerCanRemove' => $input->writerCanRemove->int() ? 'y' : 'n',
				'userCanTakeOwnership' => $input->userCanTakeOwnership->int() ? 'y' : 'n',
				'oneUserItem' => $input->oneUserItem->int() ? 'y' : 'n',
				'writerGroupCanModify' => $input->writerGroupCanModify->int() ? 'y' : 'n',
				'writerGroupCanRemove' => $input->writerGroupCanRemove->int() ? 'y' : 'n',
				'useRatings' => $input->useRatings->int() ? 'y' : 'n',
				'showRatings' => $input->showRatings->int() ? 'y' : 'n',
				'ratingOptions' => $input->ratingOptions->text(),
				'useComments' => $input->useComments->int() ? 'y' : 'n',
				'showComments' => $input->showComments->int() ? 'y' : 'n',
				'showLastComment' => $input->showLastComment->int() ? 'y' : 'n',
				'useAttachments' => $input->useAttachments->int() ? 'y' : 'n',
				'showAttachments' => $input->showAttachments->int() ? 'y' : 'n',
				'orderAttachments' => implode(',', $input->orderAttachments->word()),
				'start' => $input->start->int() ? $this->readDate($input, 'start') : 0,
				'end' => $input->end->int() ? $this->readDate($input, 'end') : 0,
				'autoCreateGroup' => $input->autoCreateGroup->int() ? 'y' : 'n',
				'autoCreateGroupInc' => $input->autoCreateGroupInc->groupname(),
				'autoAssignCreatorGroup' => $input->autoAssignCreatorGroup->int() ? 'y' : 'n',
				'autoAssignCreatorGroupDefault' => $input->autoAssignCreatorGroupDefault->int() ? 'y' : 'n',
				'autoAssignGroupItem' => $input->autoAssignGroupItem->int() ? 'y' : 'n',
				'autoCopyGroup' => $input->autoCopyGroup->int() ? 'y' : 'n',
				'viewItemPretty' => $input->viewItemPretty->text(),
				'editItemPretty' => $input->editItemPretty->text(),
				'autoCreateCategories' => $input->autoCreateCategories->int() ? 'y' : 'n',
				'publishRSS' => $input->publishRSS->int() ? 'y' : 'n',
				'sectionFormat' => $input->sectionFormat->word(),
				'adminOnlyViewEditItem' => $input->adminOnlyViewEditItem->int() ? 'y' : 'n',
				'logo' => $input->logo->text(),
				'useFormClasses' => $input->useFormClasses->int() ? 'y' : 'n',
				'formClasses' => $input->formClasses->text(),
			);

			$trackerId = $this->utilities->updateTracker($trackerId, $data);

			$cat_desc = $data['description'];
			$cat_name = $data['name'];
			$cat_href = "tiki-view_tracker.php?trackerId=" . $trackerId;
			$cat_objid = $trackerId;
			include "categorize.php";

			$groupforAlert = $input->groupforAlert->groupname();

			if ($groupforAlert) {
				$groupalertlib = TikiLib::lib('groupalert');
				$showeachuser = $input->showeachuser->int() ? 'y' : 'n';
				$groupalertlib->AddGroup('tracker', $trackerId, $groupforAlert, $showeachuser);
			}

			$definition = Tracker_Definition::get($trackerId);
		}

		include_once ("categorize_list.php");
		$trklib = TikiLib::lib('trk');
		$groupalertlib = TikiLib::lib('groupalert');
		$groupforAlert = $groupalertlib->GetGroup('tracker', 'trackerId');
		return array(
			'title' => $trackerId ? tr('Edit %0', $definition->getConfiguration('name')) : tr('Create Tracker'),
			'trackerId' => $trackerId,
			'info' => $definition->getInformation(),
			'statusTypes' => TikiLib::lib('trk')->status_types(),
			'statusList' => preg_split('//', $definition->getConfiguration('defaultStatus', 'o'), -1, PREG_SPLIT_NO_EMPTY),
			'sortFields' => $this->getSortFields($definition),
			'attachmentAttributes' => $this->getAttachmentAttributes($definition->getConfiguration('orderAttachments', 'created,filesize,hits')),
			'startDate' => $this->format($definition->getConfiguration('start'), '%Y-%m-%d'),
			'startTime' => $this->format($definition->getConfiguration('start'), '%H:%M'),
			'endDate' => $this->format($definition->getConfiguration('end'), '%Y-%m-%d'),
			'endTime' => $this->format($definition->getConfiguration('end'), '%H:%M'),
			'groupList' => $this->getGroupList(),
			'groupforAlert' => $groupforAlert,
			'showeachuser' => $groupalertlib->GetShowEachUser('tracker', 'trackerId', $groupforAlert),
			'sectionFormats' => $trklib->getGlobalSectionFormats(),
		);
	}

	function action_duplicate($input)
	{
		$confirm = $input->confirm->int();
		
		if ($confirm) {
			$trackerId = $input->trackerId->int();
			$perms = Perms::get('tracker', $trackerId);
				if (! $perms->admin_trackers || ! Perms::get()->admin_trackers) {
					throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
				}
			$definition = Tracker_Definition::get($trackerId);
				if (! $definition) {
					throw new Services_Exception_NotFound;
				}
			$name = $input->name->text();
			if (! $name) {
				throw new Services_Exception_MissingValue('name');
			}
			$newId = $this->utilities->duplicateTracker($trackerId, $name, $input->dupCateg->int(), $input->dupPerms->int());
			return array(
				'trackerId' => $newId,
				'name' => $name,
			);
		} else {
			$trackers = $this->action_list_trackers($input);
			return array(
				'title' => tr('Duplicate Tracker'),
				'trackers' => $trackers["data"],
			);
		}
	}

	function action_export($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->export_tracker) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if ($perms->admin_trackers) {
			$info = $definition->getInformation();

			$out = "[TRACKER]\n";

			foreach ($info as $key => $value) {
				if ($key && $value) {
					$out .= "$key = $value\n";
				}
			}
		} else {
			$out = null;
		}

		return array(
			'title' => tr('Export Items'),
			'trackerId' => $trackerId,
			'export' => $out,
			'fields' => $definition->getFields(),
			'recordsMax' => $definition->getConfiguration('items'),
		);
	}

	function action_export_items($input)
	{
		TikiLib::lib('tiki')->allocate_extra(
			'tracker_export_items',
			function () use ($input) {
				$trackerId = $input->trackerId->int();

				$definition = Tracker_Definition::get($trackerId);

				if (! $definition) {
					throw new Services_Exception_NotFound;
				}

				$perms = Perms::get('tracker', $trackerId);
				if (! $perms->export_tracker) {
					throw new Services_Exception_Denied(tr("You don't have permission to export"));
				}

				$fields = array();
				foreach ((array) $input->listfields->int() as $fieldId) {
					if ($f = $definition->getField($fieldId)) {
						$fields[$fieldId] = $f;
					}
				}

				if (0 === count($fields)) {
					$fields = $definition->getFields();
				}

				$showItemId = $input->showItemId->int();
				$showStatus = $input->showStatus->int();
				$showCreated = $input->showCreated->int();
				$showLastModif = $input->showLastModif->int();
				$keepItemlinkId = $input->keepItemlinkId->int();
				$keepCountryId = $input->keepCountryId->int();
				$dateFormatUnixTimestamp = $input->dateFormatUnixTimestamp->int();

				$encoding = $input->encoding->text();
				if (! in_array($encoding, array('UTF-8', 'ISO-8859-1'))) {
					$encoding = 'UTF-8';
				}
				$separator = $input->separator->none();
				$delimitorR = $input->delimitorR->none();
				$delimitorL = $input->delimitorL->none();

				$cr = $input->CR->none();

				$recordsMax = $input->recordsMax->int();
				$recordsOffset = $input->recordsOffset->int() - 1;

				$writeCsv = function ($fields) use($separator, $delimitorL, $delimitorR, $encoding, $cr) {
					$values = array();
					foreach ($fields as $v) {
						$values[] = "$delimitorL$v$delimitorR";
					}

					$line = implode($separator, $values);
					$line = str_replace(array("\r\n", "\n", "<br/>", "<br />"), $cr, $line);

					if ($encoding === 'ISO-8859-1') {
						echo utf8_decode($line) . "\n";
					} else {
						echo $line . "\n";
					}
				};

			 	session_write_close();

				$trklib = TikiLib::lib('trk');
				$trklib->write_export_header($encoding, $trackerId);

				$header = array();
				if ($showItemId) {
					$header[] = 'itemId';
				}
				if ($showStatus) {
					$header[] = 'status';
				}
				if ($showCreated) {
					$header[] = 'created';
				}
				if ($showLastModif) {
					$header[] = 'lastModif';
				}
				foreach ($fields as $f) {
					$header[] = $f['name'] . ' -- ' . $f['fieldId'];
				}

				$writeCsv($header);

				/** @noinspection PhpParamsInspection */
				$items = $trklib->list_items($trackerId, $recordsOffset, $recordsMax, 'itemId_asc', $fields);

				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_tiki_short_datetime');
				foreach ($items['data'] as $row) {
					$toDisplay = array();
					if ($showItemId) {
						$toDisplay[] = $row['itemId'];
					}
					if ($showStatus) {
						$toDisplay[] = $row['status'];
					}
					if ($showCreated) {
						if ($dateFormatUnixTimestamp) {
							$toDisplay[] = $row['created'];
						} else {
							$toDisplay[] = smarty_modifier_tiki_short_datetime($row['created'], '', 'n');
						}
					}
					if ($showLastModif) {
						if ($dateFormatUnixTimestamp) {
							$toDisplay[] = $row['lastModif'];
						} else {
							$toDisplay[] = smarty_modifier_tiki_short_datetime($row['lastModif'], '', 'n');
						}
					}
					foreach ($row['field_values'] as $val) {
						if ( ($keepItemlinkId) && ($val['type'] == 'r') ) {
							$toDisplay[] = $val['value'];
						} elseif ( ($keepCountryId) && ($val['type'] == 'y') ) {
							$toDisplay[] = $val['value'];
						} elseif ( ($dateFormatUnixTimestamp) && ($val['type'] == 'f') ) {
							$toDisplay[] = $val['value'];
						} elseif ( ($dateFormatUnixTimestamp) && ($val['type'] == 'j') ) {
							$toDisplay[] = $val['value'];
						} else {
							$toDisplay[] = $trklib->get_field_handler($val, $row)->renderOutput(array(
								'list_mode' => 'csv',
								'CR' => $cr,
								'delimitorL' => $delimitorL,
								'delimitorR' => $delimitorR,
							));
						}
					}

					$writeCsv($toDisplay);
				}
			}
		);

		exit;
	}

	function action_dump_items($input)
	{
		$trackerId = $input->trackerId->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->export_tracker) {
			throw new Services_Exception_Denied(tr("You don't have permission to export"));
		}

		$trklib = TikiLib::lib('trk');
		$trklib->dump_tracker_csv($trackerId);
		exit;
	}

	function action_export_profile($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$trackerId = $input->trackerId->int();

		$profile = Tiki_Profile::fromString('dummy', '');
		$data = array();
		$profileObject = new Tiki_Profile_Object($data, $profile);
		$profileTrackerInstallHandler = new Tiki_Profile_InstallHandler_Tracker($profileObject, array());

		$export_yaml = $profileTrackerInstallHandler->_export($trackerId, $profileObject);

		include_once 'lib/wiki-plugins/wikiplugin_code.php';
		$export_yaml = wikiplugin_code($export_yaml, array('caption' => 'YAML', 'colors' => 'yaml'));
		$export_yaml = preg_replace('/~[\/]?np~/', '', $export_yaml);

		return array(
			'trackerId' => $trackerId,
			'yaml' => $export_yaml,
		);
	}

	private function trackerName($trackerId)
	{
		return TikiLib::lib('tiki')->table('tiki_trackers')->fetchOne('name', array('trackerId' => $trackerId));
	}

	private function trackerId($trackerName)
	{
		return TikiLib::lib('tiki')->table('tiki_trackers')->fetchOne('trackerId', array('name' => $trackerName));
	}

	private function trackerNameAndId(&$trackerId, &$trackerName)
	{
		if ($trackerId > 0 && empty($trackerName)) {
			$trackerName = $this->trackerName($trackerId);
		} elseif ($trackerId < 1 && !empty($trackerName)) {
			$trackerId = $this->trackerId($trackerName);
		}
	}

	function action_import($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}
		
		unset($success);
		$confirm = $input->confirm->int();
		
		if ($confirm) {
				
			$raw = $input->raw->none();
			$preserve = $input->preserve->int();

			$data = TikiLib::lib('tiki')->read_raw($raw);

			if (! $data || ! isset($data['tracker'])) {
				throw new Services_Exception(tr('Invalid data provided'), 400);
			}

			$data = $data['tracker'];

			$trackerId = 0;
			if ($preserve) {
				$trackerId = (int) $data['trackerId'];
			}

			unset($data['trackerId']);
			$trackerId = $this->utilities->updateTracker($trackerId, $data);
			$success = 1;
			
			return array(
				'trackerId' => $trackerId,
				'name' => $data['name'],
				'success' => $success,
			);
		} 
		
		return array(
			'title' => tr('Import Tracker Structure'),
			'modal' => $input->modal->int(),
		);
	}

	function action_import_items($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved for tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (isset($_FILES['importfile'])) {
			if (! is_uploaded_file($_FILES['importfile']['tmp_name'])) {
				throw new Services_Exception(tr('File upload failed.'), 400);
			}

			if (! $fp = @ fopen($_FILES['importfile']['tmp_name'], "rb")) {
				throw new Services_Exception(tr('Uploaded file could not be read.'), 500);
			}

			$trklib = TikiLib::lib('trk');
			$count = $trklib->import_csv(
				$trackerId,
				$fp,
				($input->add_items->int() !== 1), // checkbox is "Create as new items" - param is replace_rows
				$input->dateFormat->text(),
				$input->encoding->text(),
				$input->separator->text(),
				$input->updateLastModif->int(),
				$input->convertItemLinkValues->int()
			);

			fclose($fp);

			return array(
				'trackerId' => $trackerId,
				'return' => $count,
				'importfile' => $_FILES['importfile']['name'],
			);
		}

		return array(
			'title' => tr('Import Items'),
			'trackerId' => $trackerId,
			'return' => '',
		);
	}

	function action_vote($input)
	{
		$requestData = array();
		$requestData['itemId'] = $input->i->int();
		$requestData['fieldId'] = $input->f->int();
		$requestData['vote'] = 'y';

		$v = $input->v->text();
		if ($v !== 'NULL') {
			$v = $input->v->int();
		}
		$requestData['ins_' . $requestData['fieldId']] = $v;

		$trklib = TikiLib::lib('trk');
		$field = $trklib->get_tracker_field($requestData['fieldId']);

		$handler = $trklib->get_field_handler($field);

		$result = $handler->getFieldData($requestData);

		return array($result);
	}

	public function action_import_profile($input)
	{
		$tikilib = TikiLib::lib('tiki');

		$perms = Perms::get();
		if (! $perms->admin) {
			throw new Services_Exception_Denied(tr('Reserved for administrators'));
		}
		
		unset($success);
		$confirm = $input->confirm->int();
		
		if ($confirm) {
		
			$transaction = $tikilib->begin();
			$installer = new Tiki_Profile_Installer;

			$yaml = $input->yaml->text();
			$name = "tracker_import:" . md5($yaml);
			$profile = Tiki_Profile::fromString('{CODE(caption="yaml")}' . "\n" . $yaml . "\n" . '{CODE}', $name);

			if ($installer->isInstallable($profile) == true) {
				if ($installer->isInstalled($profile) == true) {
					$installer->forget($profile);
				}

				$installer->install($profile);
				$feedback = $installer->getFeedback();
				$transaction->commit();
				return $feedback;
				$success=1;
			} else {
				return false;
			}
		}
		return array(
			'title' => tr('Import Tracker From Profile/YAML'),
			'modal' => $input->modal->int(),
		);
	}

	private function getSortFields($definition)
	{
		$sorts = array();

		foreach ($definition->getFields() as $field) {
			$sorts[$field['fieldId']] = $field['name'];
		}

		$sorts[-1] = tr('Last Modification');
		$sorts[-2] = tr('Creation Date');
		$sorts[-3] = tr('Item ID');

		return $sorts;
	}

	private function getAttachmentAttributes($active)
	{
		$active = explode(',', $active);

		$available = array(
			'filename' => tr('Filename'),
			'created' => tr('Creation date'),
			'hits' => tr('Views'),
			'comment' => tr('Comment'),
			'filesize' => tr('File size'),
			'version' => tr('Version'),
			'filetype' => tr('File type'),
			'longdesc' => tr('Long description'),
			'user' => tr('User'),
		);

		$active = array_intersect(array_keys($available), $active);

		$attributes = array_fill_keys($active, null);
		foreach ($available as $key => $label) {
			$attributes[$key] = array('label' => $label, 'selected' => in_array($key, $active));
		}

		return $attributes;
	}

	private function readDate($input, $prefix)
	{
		$date = $input->{$prefix . 'Date'}->text();
		$time = $input->{$prefix . 'Time'}->text();

		if (! $time) {
			$time = '00:00';
		}

		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute) = explode(':', $time);
		$second = 0;

		$tikilib = TikiLib::lib('tiki');
		$tikidate = TikiLib::lib('tikidate');
		$display_tz = $tikilib->get_display_timezone();
		if ( $display_tz == '' ) $display_tz = 'UTC';
		$tikidate->setTZbyID($display_tz);
		$tikidate->setLocalTime($day, $month, $year, $hour, $minute, $second, 0);
		return $tikidate->getTime();
	}

	private function format($date, $format)
	{
		if ($date) {
			return TikiLib::date_format($format, $date);
		}
	}

	private function getGroupList()
	{
		$userlib = TikiLib::lib('user');
		$groups = $userlib->list_all_groupIds();
		$out = array();

		foreach ($groups as $g) {
			$out[] = $g['groupName'];
		}

		return $out;
	}
	
	function action_select_tracker($input)
	{
		$confirm = $input->confirm->int();
		
		if ($confirm) {
			$trackerId = $input->trackerId->int();
			return array(
				'FORWARD' => array(
						'action' => 'insert_item',
						'trackerId' => $trackerId,
				),
			);
		}
		else {
			$trklib = TikiLib::lib('trk');
			$trackers = $trklib->list_trackers();
			return array(
				'title' => tr('Select Tracker'),
				'trackers' => $trackers["data"],
			);
		}
	}

	function action_search_help($input)
	{
		return [
			'title' => tr('Help'),
		];
	}

	function get_validation_options()
	{
		return ',
		errorClass: "label label-warning",
		errorPlacement: function(error,element) {
			if ($(element).parents(".input-group").length > 0) {
				error.insertAfter($(element).parents(".input-group").first());
			} else if ($(element).parents(".has-error").length > 0) {
				error.appendTo($(element).parents(".has-error").first());
			} else {
				error.insertAfter(element);
			}
		},
		highlight: function(element) {
			$(element).parents("div, p").first().addClass("has-error");
		},
		unhighlight: function(element) {
			$(element).parents("div, p").first().removeClass("has-error");
		},
		ignore: ".ignore"
		});';
	}	
}

