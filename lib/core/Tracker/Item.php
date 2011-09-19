<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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

	public static function fromInfo($info)
	{
		$obj = new self;
		$obj->info = $info;
		$obj->definition = Tracker_Definition::get($info['trackerId']);
		$obj->initialize();

		return $obj;
	}

	private function __construct()
	{
	}

	function canView()
	{
		if ($this->canModifyFromSpecialPermissions()) {
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
		if ($this->canModifyFromSpecialPermissions()) {
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
		if ($status == 'c') {
			return $this->perms->remove_tracker_items_closed;
		} elseif ($status == 'p') {
			return $this->perms->remove_tracker_items_pending;
		} else {
			return $this->perms->remove_tracker_items;
		}
	}

	private function canModifyFromSpecialPermissions()
	{
		global $user;
		if ($user && $this->owner && $user === $this->owner) {
			return true;
		}

		if ($this->ownerGroup && in_array($this->ownerGroup, $this->perms->getGroups())) {
			return true;
		}

		return false;
	}

	private function initialize()
	{
		$this->owner = $this->getItemOwner();
		$this->ownerGroup = $this->getItemGroupOwner();
		$this->perms = Perms::get('tracker', $this->info['trackerId']);
	}

	private function getItemOwner()
	{
		global $prefs;

		if ($prefs['userTracker'] != 'y') {
			return null;
		}

		if ($this->definition->getConfiguration('writerCanModify') != 'y') {
			return null;
		}

		$userField = $this->definition->getUserField();
		if ($userField) {
			return $this->info[$userField];
		}
	}

	private function getItemGroupOwner()
	{
		global $prefs;

		if ($prefs['groupTracker'] != 'y') {
			return null;
		}

		if ($this->definition->getConfiguration('writerGroupCanModify') != 'y') {
			return null;
		}

		$groupField = $this->definition->getWriterGroupField();
		if ($groupField) {
			return $this->info[$groupField];
		}
	}

	function canViewField($fieldId)
	{
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

		if ($isHidden == 'c' && $this->canModifyFromSpecialPermissions()) {
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
		// Nothing stops the tracker administrator from doing anything
		if ($this->perms->admin_trackers) {
			//return true;
		}

		// Modify the item is required to modify the field (safety)
		if (! $this->canModify()) {
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
			return $this->canModifyFromSpecialPermissions();
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
}

