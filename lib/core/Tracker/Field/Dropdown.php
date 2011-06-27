<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for dropdown
 * 
 * Letter key: ~d~ ~D~
 *
 */
class Tracker_Field_Dropdown extends Tracker_Field_Abstract
{
	private $type;

	public static function getTypes()
	{
		return array(
			'd' => array(
				'name' => tr('Drop Down'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option'),
						'filter' => 'text',
						'count' => '*',
					),
				),
			),
			'D' => array(
				'name' => tr('Drop Down with Other field'),
				'description' => tr('Allows users to select from a specified set of options or to enter an alternate option'),
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option'),
						'filter' => 'text',
						'count' => '*',
					),
				),
			),
			'R' => array(
				'name' => tr('Radio Buttons'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option'),
						'filter' => 'text',
						'count' => '*',
					),
				),
			),
		);
	}
	
	function __construct($fieldInfo, $itemData, $trackerDefinition, $type = '')
	{
		$this->type = $type;
		parent::__construct($fieldInfo, $itemData, $trackerDefinition);
	}

	function getFieldData(array $requestData = array())
	{
		
		$ins_id = $this->getInsertId();

		if (!empty($requestData['other_'.$this->getInsertId()])) {
			$value = $requestData['other_'.$this->getInsertId()];
		} elseif (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue();
		}
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/dropdown.tpl', $context);
	}
}

