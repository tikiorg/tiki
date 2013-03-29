<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for simple fields:
 * 
 * - email key ~m~
 */
class Tracker_Field_Simple extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	private $type;

	public static function getTypes()
	{
		return array(
			'm' => array(
				'name' => tr('Email'),
				'description' => tr('Allows to input an email address with options of making it active.'),
				'help' => 'Email Tracker Field',				
				'prefs' => array('trackerfield_email'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'link' => array(
						'name' => tr('Link Type'),
						'description' => tr('How the email address will be rendered.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Plain text'),
							1 => tr('Encoded mailto link'),
							2 => tr('Simple mailto link'),
						),
					),
					'watchopen' => array(
						'name' => tr('Watch Open'),
						'description' => tr('Notify this address every time the status changes to open.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'o' => tr('Yes'),
						),
					),
					'watchpending' => array(
						'name' => tr('Watch Pending'),
						'description' => tr('Notify this address every time the status changes to pending.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'p' => tr('Yes'),
						),
					),
					'watchopen' => array(
						'name' => tr('Watch Closed'),
						'description' => tr('Notify this address every time the status changes to closed.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'c' => tr('Yes'),
						),
					),
				),
			),
		);
	}
	
	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		switch ($type) {
			case 'm':
				return new Tracker_Field_Simple($fieldInfo, $itemData, $trackerDefinition, 'email');
		}
	}
	
	function __construct($fieldInfo, $itemData, $trackerDefinition, $type)
	{
		$this->type = $type;
		parent::__construct($fieldInfo, $itemData, $trackerDefinition);
	}
	
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate("trackerinput/{$this->type}.tpl", $context);
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}
}

