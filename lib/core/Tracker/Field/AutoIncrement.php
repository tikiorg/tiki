<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Auto increment
 * 
 * Letter key: ~q~
 *
 */
class Tracker_Field_AutoIncrement extends Tracker_Field_Abstract implements Tracker_Field_Exportable, Tracker_Field_Filterable
{
	public static function getTypes()
	{
		return array(
			'q' => array(
				'name' => tr('Auto-Increment'),
				'description' => tr('Allows an incrementing value field, or itemId field.'),
				'readonly' => true,
				'help' => 'Auto-Increment Field',				
				'prefs' => array('trackerfield_autoincrement'),
				'tags' => array('advanced'),
				'default' => 'n',
				'supported_changes' => array('d', 'D', 'R', 'M', 't', 'a', 'n', 'q', 'b'),
				'params' => array(
					'start' => array(
						'name' => tr('Start'),
						'description' => tr('The starting value for the field'),
						'default' => 1,
						'filter' => 'int',
						'legacy_index' => 0,
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text that will be displayed before the field'),
						'filter' => 'text',
						'legacy_index' => 1,
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text that will be displayed after the field'),
						'filter' => 'text',
						'legacy_index' => 2,
					),
					'itemId' => array(
						'name' => tr('Item ID'),
						'description' => tr('If set to "itemId", will set this field to match the value of the actual database itemId field value'),
						'filter' => 'alpha',
						'options' => array(
							'' => '',
							'itemId' => 'itemId',
						),
						'legacy_index' => 3,
					),
					'update' => array(
						'name' => tr('Update Empty'),
						'description' => tr("Add auto-increment numbers to items in this tracker that don't have one one. ********** N.B. This modifies data and there is no undo **********"),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$value = isset($requestData[$ins_id]) ? $requestData[$ins_id] : $this->getValue();

		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/autoincrement.tpl', $context);
	}

	protected function renderInnerOutput($context = array())
	{
		$value = $this->getValue();
		$prepend = $this->getOption('prepend');
		if (!empty($prepend)) {
			if( $context['list_mode'] !== 'csv' ) {
				$value = "<span class='formunit'>$prepend</span>" . $value;
			} else {
				$value = $prepend . $value;
			}
		}
	
		$append = $this->getOption('append');
		if (!empty($append)) {
			if( $context['list_mode'] !== 'csv' ) {
				$value .= "<span class='formunit'>$append</span>";
			} else {
				$value .= $append;
			}
		}

		return $value;
	}

	function handleSave($value, $oldValue)
	{
		$value = false;
		if ($this->getOption('itemId') == 'itemId') {
			$value = $this->getItemId();
		} elseif (is_null($oldValue)) {
			$value = TikiLib::lib('trk')->get_maximum_value($this->getConfiguration('fieldId'));
			if (! $value) {
				$value = $this->getOption('start', 1);
			} else {
				$value += 1;
			}
		}

		return array(
			'value' => $value,
		);
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$prepend = $this->getOption('prepend');
		$append = $this->getOption('append');

		$schema->addNew($permName, 'default')
			->setLabel($this->getConfiguration('name'))
			->setRenderTransform(function ($value) {
				return $value;
			})
			;
		$schema->addNew($permName, 'formatted')
			->setLabel($this->getConfiguration('name'))
			->addIncompatibility($permName, 'default')
			->setRenderTransform(function ($value) use ($prepend, $append) {
				return $prepend . $value . $append;
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
					$query->filterIdentifier($value, $baseKey);
				}
			})
			;

		return $filters;
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$item = $this->getValue();
		$baseKey = $this->getBaseKey();
		$prepend = $this->getOption('prepend');
		$append = $this->getOption('append');

		$out = array(
			$baseKey => $typeFactory->numeric($item),
			"{$baseKey}_text" => $typeFactory->sortable($prepend.$item.$append),
		);
		return $out;
	}

	// if we need to update after field save then do it here
	function handleFieldSave($data)
	{
		if ($this->getOption('update')) {

			$trklib = TikiLib::lib('trk');
			$searchlib = TikiLib::lib('unifiedsearch');

			$trackerId = $this->getConfiguration('trackerId');
			$fieldId = $this->getConfiguration('fieldId');

			$tiki_tracker_items = TikiDb::get()->table('tiki_tracker_items');
			$tiki_tracker_item_fields = TikiDb::get()->table('tiki_tracker_item_fields');

			$options = json_decode($data['options'], true);	// get the start index, might have been updated in the field save
			$value = empty($options['start']) ? 1 : $options['start'];
			$count = 0;

			$itemIds = $tiki_tracker_items->fetchColumn(
				'itemId',
				['trackerId' => $trackerId],
				-1,
				0,
				['created' => 'ASC']
			);
			$autoIncValues = $tiki_tracker_item_fields->fetchMap(
				'itemId',
				'value',
				['fieldId' => $fieldId]
			);

			$tx = TikiDb::get()->begin();

			foreach ($itemIds as $itemId) {
				if (empty($autoIncValues[$itemId])) {
					while (array_search($value, $autoIncValues) !== false) {
						// this value already exists
						$value++;
					}

					$trklib->modify_field($itemId, $fieldId, $value);
					$searchlib->invalidateObject('trackeritem', $itemId);

					$value++;
					$count++;
				}
			}

			$tx->commit();

			if ($count) {
				Feedback::warning(tr('Note: %0 auto-increment item values updated', $count), 'session');
			}

		}
	}
}

