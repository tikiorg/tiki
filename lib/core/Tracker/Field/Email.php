<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for simple fields:
 * 
 * - email key ~m~
 */
class Tracker_Field_Email extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable, Tracker_Field_Filterable
{
	private $type;

	public static function getTypes()
	{
		return array(
			'm' => array(
				'name' => tr('Email'),
				'description' => tr('Allows an email address to be input with the option of making it active.'),
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
						'legacy_index' => 0,
					),
					'watchopen' => array(
						'name' => tr('Watch Open'),
						'description' => tr('Notify this address every time the status changes to open.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'o' => tr('Yes'),
						),
						'legacy_index' => 1,
					),
					'watchpending' => array(
						'name' => tr('Watch Pending'),
						'description' => tr('Notify this address every time the status changes to pending.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'p' => tr('Yes'),
						),
						'legacy_index' => 2,
					),
					'watchclosed' => array(
						'name' => tr('Watch Closed'),
						'description' => tr('Notify this address every time the status changes to closed.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'c' => tr('Yes'),
						),
						'legacy_index' => 3,
					),
				),
			),
		);
	}
	
	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		switch ($type) {
			case 'm':
				return new self($fieldInfo, $itemData, $trackerDefinition, 'email');
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
	
	public function renderOutput($context = array())
	{
		$opt = $this->getOption('link');
		$value = $this->getValue();

		if ($opt == 0 || $context['list_mode'] == 'csv' || empty($value)) {
			return $value;
		} else {
			if ($opt == 1) {
				$ar = explode('@', $value);
				return TikiLib::lib('tiki')->protect_email($ar[0], $ar[1]);
			} else {	// link == 2
				return "<a href=\"mailto:$value\">$value</a>";
			}
		}
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

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_escape');

		$schema->addNew($permName, 'default')
			->setLabel($this->getConfiguration('name'))
			->setRenderTransform(function ($value) {
				return $value;
			})
			;
		$schema->addNew($permName, 'mailto')
			->setLabel($this->getConfiguration('name'))
			->setPlainReplacement('default')
			->setRenderTransform(function ($value) {
				$escape = smarty_modifier_escape($value);
				return "<a href=\"mailto:$escape\">$escape</a>";
			})
			;

		return $schema;
	}

	function getFilterCollection()
	{
		$filters = new Tracker\Filter\Collection($this->getTrackerDefinition());
		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');
		$baseKey = $this->getBaseKey();


		$filters->addNew($permName, 'lookup')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\TextField("tf_{$permName}_lookup"))
			->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
				$value = $control->getValue();

				if ($value) {
					$query->filterContent($value, $baseKey);
				}
			})
			;

		return $filters;
	}
}

