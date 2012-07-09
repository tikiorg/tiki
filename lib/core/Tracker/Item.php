<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Item
{
	private $info;
	private $definition;

	private $owner;
	private $ownerGroup;
	private $perms;

	public static function fromId($itemId)
	{
		$info = TikiLib::lib('trk')->get_tracker_item($itemId);

		if ($info) {
			return self::fromInfo($info);
		}
	}

	public static function fromInfo($info)
	{
		$obj = new self;
		if (empty($info['trackerId']) && !empty($info['itemId'])) {
			$info['trackerId'] = TikiLib::lib('trk')->get_tracker_for_item($info['itemId']);
		}
		$obj->info = $info;
		$obj->definition = Tracker_Definition::get($info['trackerId']);
		$obj->initialize();

		return $obj;
	}

	public static function newItem($trackerId)
	{
		$obj = new self;
		$obj->info = array();
		$obj->definition = Tracker_Definition::get($trackerId);
		$obj->initialize();

		return $obj;
	}

	private function __construct()
	{
	}

	function canView()
	{
		if ($this->isNew()) {
			return true;
		}

		if ($this->canFromSpecialPermissions('Modify')) {
			return true;
		}

		$status = $this->info['status'];

		if ($status == 'c') {
			return $this->perms->view_trackers_closed;
		} elseif ($status == 'p') {
			return $this->perms->view_trackers_pending;
		} else {
			return $this->perms->view_trackers;
		}
	}

	function canModify()
	{
		if ($this->isNew()) {
			return $this->perms->create_tracker_items;
		}

		if ($this->canFromSpecialPermissions('Modify')) {
			return true;
		}

		$status = $this->info['status'];

		if ($status == 'c') {
			return $this->perms->modify_tracker_items_closed;
		} elseif ($status == 'p') {
			return $this->perms->modify_tracker_items_pending;
		} else {
			return $this->perms->modify_tracker_items;
		}
	}

	function canRemove()
	{
		if ($this->isNew()) {
			return false;
		}

		if ($this->canFromSpecialPermissions('Remove')) {
			return true;
		}

		$status = $this->info['status'];

		if ($status == 'c') {
			return $this->perms->remove_tracker_items_closed;
		} elseif ($status == 'p') {
			return $this->perms->remove_tracker_items_pending;
		} else {
			return $this->perms->remove_tracker_items;
		}
	}

	private function canFromSpecialPermissions($operation)
	{
		global $user;
		if ($this->definition->getConfiguration('writerCan' . $operation, 'n') == 'y' && $user && $this->owner && $user === $this->owner) {
			return true;
		}

		if ($this->definition->getConfiguration('writerGroupCan' . $operation, 'n') == 'y' && $this->ownerGroup && in_array($this->ownerGroup, $this->perms->getGroups())) {
			return true;
		}

		return false;
	}

	private function initialize()
	{
		$this->owner = $this->getItemOwner();
		$this->ownerGroup = $this->getItemGroupOwner();

		$this->perms = $this->getItemPermissions();

		if (! $this->perms) {
			$this->perms = $this->getTrackerPermissions();
		}
	}

	private function getTrackerPermissions()
	{
		if ($this->definition) {
			$trackerId = $this->definition->getConfiguration('trackerId');
			return Perms::get('tracker', $trackerId);
		}

		$accessor = new Perms_Accessor;
		$accessor->setResolver(new Perms_Resolver_Default(false));
		return $accessor;
	}

	private function getItemPermissions()
	{
		if (! $this->isNew()) {
			$itemId = $this->info['itemId'];

			$perms = Perms::get('trackeritem', $itemId);
			$resolver = $perms->getResolver();
			if (method_exists($resolver, 'from') && $resolver->from() != '') {
				// Item permissions are valid if they are assigned directly to the object or category, otherwise
				// tracker permissions are better than global ones.
				return $perms;
			}
		}
	}

	private function getItemOwner()
	{
		if (!is_object($this->definition)) {
			return; // TODO: This is a temporary fix, we should be able to getItemOwner always
		}
		
		$userField = $this->definition->getUserField();
		if ($userField) {
			return $this->getValue($userField);
		}
	}

	private function getItemGroupOwner()
	{
		if (!is_object($this->definition)) {
			return; // TODO: This is a temporary fix, we should be able to getItemOwner always
		}
		
		$groupField = $this->definition->getWriterGroupField();
		if ($groupField) {
			return $this->getValue($groupField);
		}
	}

	function canViewField($fieldId)
	{
		$fieldId = $this->prepareFieldId($fieldId);

		// Nothing stops the tracker administrator from doing anything
		if ($this->perms->admin_trackers) {
			return true;
		}

		// Viewing the item is required to view the field (safety)
		if (! $this->canView()) {
			return false;
		}

		$field = $this->definition->getField($fieldId);
		
		if (! $field) {
			return false;
		}

		$isHidden = $field['isHidden'];
		$visibleBy = $field['visibleBy'];

		if ($isHidden == 'c' && $this->canFromSpecialPermissions('Modify')) {
			// Creator or creator group check when field can be modified by creator only
			return true;
		} elseif ($isHidden == 'y') {
			// Visible by administrator only
			return false;
		} else {
			// Permission based on visibleBy apply
			return $this->isMemberOfGroups($visibleBy);
		}
	}

	function canModifyField($fieldId)
	{
		$fieldId = $this->prepareFieldId($fieldId);

		// Nothing stops the tracker administrator from doing anything
		if ($this->perms->admin_trackers) {
			return true;
		}

		// Modify the item is required to modify the field (safety)
		if (! $this->canModify()) {
			return false;
		}

		// Cannot modify a field you are not supposed to see
		// Modify without view means insert-only
		if (! $this->isNew() && ! $this->canViewField($fieldId)) {
			return false;
		}

		$field = $this->definition->getField($fieldId);
		
		if (! $field) {
			return false;
		}

		$isHidden = $field['isHidden'];
		$editableBy = $field['editableBy'];

		if ($isHidden == 'c') {
			// Creator or creator group check when field can be modified by creator only
			return $this->canFromSpecialPermissions('Modify');
		} elseif ($isHidden == 'p') {
			// Editable by administrator only
			return false;
		} else {
			// Permission based on editableBy apply
			return $this->isMemberOfGroups($editableBy);
		}
	}

	private function isMemberOfGroups($groups)
	{
		// Nothing specified means everyone
		if (empty($groups)) {
			return true;
		}

		$commonGroups = array_intersect($groups, $this->perms->getGroups());
		return count($commonGroups) != 0;
	}

	private function getValue($fieldId)
	{
		if (isset($this->info[$fieldId])) {
			return $this->info[$fieldId];
		}
	}

	private function isNew()
	{
		return empty($this->info);
	}

	public function prepareInput($input)
	{
		$input = $input->none();
		$fields = $this->definition->getFields();
		$output = array();

		$factory = $this->definition->getFieldFactory();
		foreach ($fields as $field) {
			$fid = $field['fieldId'];

			if ($this->canModifyField($fid)) {
				$field['ins_id'] = "ins_$fid";

				$handler = $factory->getHandler($field, $this->info);
				$output[] = array_merge($field, $handler->getFieldData($input));
			}
		}
		
		return $output;
	}

	private function prepareFieldId($fieldId)
	{
		if (TikiLib::startsWith($fieldId, 'ins_') == true) {
			$fieldId = str_replace('ins_', '', $fieldId);
		}

		if (! is_numeric($fieldId)) {
			if ($field = $this->definition->getFieldFromPermName($fieldId)) {
				$fieldId = $field['fieldId'];
			}
		}

		return $fieldId;
	}
	
	/**
	 * Getter method for the permissions of this
	 * item.
	 * 
	 * @param string $permName
	 * @return bool|null
	 */
	public function getPerm($permName)
	{
		return isset($this->perms->$permName) ? $this->perms->$permName : null;
	}

	public function getPerms()
	{
		return $this->perms;
	}
}

