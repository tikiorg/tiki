<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler to perform a calculation for the tracker entry.
 * 
 * Letter key: ~GF~
 *
 */
class Tracker_Field_Math extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Indexable, Tracker_Field_Exportable
{
	private static $runner;

	public static function getTypes()
	{
		return array(
			'math' => array(
				'name' => tr('Mathematical Calculation'),
				'description' => tr('Performs a calculation upon saving the item based on other fields within the same item.'),
				'help' => 'Mathematical Calculation Field',
				'prefs' => array('trackerfield_math'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'calculation' => array(
						'name' => tr('Calculation'),
						'type' => 'textarea',
						'description' => tr('Calculation in the Rating Language'),
						'filter' => 'text',
						'legacy_index' => 0,
					),
					'recalculate' => array(
						'name' => tr('Re-calculation event'),
						'type' => 'list',
						'description' => tr('Allow specifying special calculation handling. Selection of indexing is useful for dynamic score fields that will not be displayed.'),
						'filter' => 'word',
						'options' => array(
							'save' => tr('Save'),
							'index' => tr('Indexing'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		if (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue();
		}

		return array(
			'value' => $value,
		);
	}

	function renderInput($context = array())
	{
		return tr('Value will be re-calculated on save. Current value: %0', $this->getValue());
	}

	function renderOutput($context = array())
	{
		return $this->getValue();
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

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$value = $this->getValue();

		if ('index' == $this->getOption('recalculate')) {
			$runner = $this->getFormulaRunner();
			$data = [];

			foreach ($runner->inspect() as $fieldName) {
				if( is_string($fieldName) || is_numeric($fieldName) )
					$data[$fieldName] = $this->getItemField($fieldName);
			}

			$runner->setVariables($data);

			$value = $runner->evaluate();

			if( $value !== $this->getValue() ) {
				$trklib = TikiLib::lib('trk');
				$trklib->modify_field($this->getItemId(), $this->getConfiguration('fieldId'), $value);
			}
		}

		$baseKey = $this->getBaseKey();
		return array(
			$baseKey => $typeFactory->sortable($value),
		);
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return array($baseKey);
	}

	function getGlobalFields()
	{
		return array();
	}

	function handleFinalSave(array $data)
	{
		try {
			$runner = $this->getFormulaRunner();
			$runner->setVariables($data);

			return $runner->evaluate();
		} catch (Math_Formula_Exception $e) {
			return $e->getMessage();
		}
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$schema->addNew($permName, 'default')
			->setLabel($this->getConfiguration('name'))
			->setRenderTransform(function ($value) {
				return $value;
			})
			;

		return $schema;
	}

	private function getFormulaRunner()
	{
		static $cache = array();
		$fieldId = $this->getConfiguration('fieldId');
		if (! isset($cache[$fieldId])) {
			$cache[$fieldId] = $this->getOption('calculation');
		}

		$runner = self::getRunner();

		$cache[$fieldId] = $runner->setFormula($cache[$fieldId]);

		return $runner;
	}

	public static function getRunner()
	{
		if (! self::$runner) {
			self::$runner = new Math_Formula_Runner(
				array(
					'Math_Formula_Function_' => '',
					'Tiki_Formula_Function_' => '',
				)
			);
		}

		return self::$runner;
	}

	public static function resetRunner()
	{
		self::$runner = null;
	}
}

