<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Factory
{
	private $trackerDefinition;
	private $typeMap = array();
	private $infoMap = array();

	function __construct($trackerDefinition)
	{
		$this->trackerDefinition = $trackerDefinition;

		$fieldMap = $this->buildTypeMap(array(
			'lib/core/Tracker/Field' => 'Tracker_Field_',
		));
	}

	private function buildTypeMap($paths)
	{
		foreach ($paths as $path => $prefix) {
			foreach (glob("$path/*.php") as $file) {
				$class = $prefix . substr($file, strlen($path) + 1, -4);
				$reflected = new ReflectionClass($class);

				if ($reflected->isInstantiable() && $reflected->implementsInterface('Tracker_Field_Interface')) {
					$providedFields = $class::getTypes();

					foreach ($providedFields as $key => $info) {
						$this->typeMap[$key] = $class;
						$this->infoMap[$key] = $info;
					}
				}
			}
		}
	}

	function getFieldTypes()
	{
		return $this->infoMap;
	}

	function getHandler($field_info, $itemData = array())
	{
		switch ($field_info['type']) {
			case 'A':
				return new Tracker_Field_File($field_info, $itemData, $this->trackerDefinition);
			case 'a':
				return new Tracker_Field_TextArea($field_info, $itemData, $this->trackerDefinition);
			case 'C':
				return new Tracker_Field_Computed($field_info, $itemData, $this->trackerDefinition);
			case 'c':
				return new Tracker_Field_Checkbox($field_info, $itemData, $this->trackerDefinition);
			case 'd':
				return new Tracker_Field_Dropdown($field_info, $itemData, $this->trackerDefinition);
			case 'D':
				return new Tracker_Field_Dropdown($field_info, $itemData, $this->trackerDefinition, 'other');
			case 'R':
				return new Tracker_Field_Dropdown($field_info, $itemData, $this->trackerDefinition, 'radio');
			case 'e':
				return new Tracker_Field_Category($field_info, $itemData, $this->trackerDefinition);
			case 'FG':
				return new Tracker_Field_Files($field_info, $itemData, $this->trackerDefinition);
			case 'F':
				return new Tracker_Field_Freetags($field_info, $itemData, $this->trackerDefinition);
			case 'f':
				return new Tracker_Field_DateTime($field_info, $itemData, $this->trackerDefinition);
			case 'G':
				return new Tracker_Field_Location($field_info, $itemData, $this->trackerDefinition);
			case 'g':
				return new Tracker_Field_GroupSelector($field_info, $itemData, $this->trackerDefinition);
			case 'h':
				return new Tracker_Field_Header($field_info, $itemData, $this->trackerDefinition);
			case 'i':
				return new Tracker_Field_Image($field_info, $itemData, $this->trackerDefinition);
			case 'j':
				return new Tracker_Field_JsCalendar($field_info, $itemData, $this->trackerDefinition);
			case 'I':
				return new Tracker_Field_Simple($field_info, $itemData, $this->trackerDefinition, 'ip');
			case 'L':
				return new Tracker_Field_Url($field_info, $itemData, $this->trackerDefinition);
			case 'k':
				return new Tracker_Field_PageSelector($field_info, $itemData, $this->trackerDefinition);
			case 'l':
				return new Tracker_Field_ItemsList($field_info, $itemData, $this->trackerDefinition);
			case 'm':
				return new Tracker_Field_Simple($field_info, $itemData, $this->trackerDefinition, 'email');
			case 'N':
				return new Tracker_Field_InGroup($field_info, $itemData, $this->trackerDefinition);
			case 'n':
			case 'b':
				return new Tracker_Field_Numeric($field_info, $itemData, $this->trackerDefinition);
			case 'P':
				return new Tracker_Field_Ldap($field_info, $itemData, $this->trackerDefinition);
			case 'p':
				return new Tracker_Field_UserPreference($field_info, $itemData, $this->trackerDefinition);			
			case 'q':
				return new Tracker_Field_AutoIncrement($field_info, $itemData, $this->trackerDefinition);
			case 'r':
				return new Tracker_Field_ItemLink($field_info, $itemData, $this->trackerDefinition);
			case 's':
			case '*':
				return new Tracker_Field_Rating($field_info, $itemData, $this->trackerDefinition);
			case 'S':
				return new Tracker_Field_StaticText($field_info, $itemData, $this->trackerDefinition);
			case 't':
				return new Tracker_Field_Text($field_info, $itemData, $this->trackerDefinition);
			case 'u':
				return new Tracker_Field_UserSelector($field_info, $itemData, $this->trackerDefinition);
			case 'usergroups':
				return new Tracker_Field_UserGroups($field_info, $itemData, $this->trackerDefinition);
			case 'x':
				return new Tracker_Field_Action($field_info, $itemData, $this->trackerDefinition);
			case 'y':
				return new Tracker_Field_CountrySelector($field_info, $itemData, $this->trackerDefinition);
			case 'U':
				return new Tracker_Field_UserSubscription($field_info, $itemData, $this->trackerDefinition);
			case 'W':
				return new Tracker_Field_WebService($field_info, $itemData, $this->trackerDefinition);
			case 'w':
				return new Tracker_Field_DynamicList($field_info, $itemData, $this->trackerDefinition);
			case 'REL':
				return new Tracker_Field_Relation($field_info, $itemData, $this->trackerDefinition);
		}
	}
}

