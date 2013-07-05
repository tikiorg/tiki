<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_Dropdown extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Search_FacetProvider_Interface
{
	public static function getTypes()
	{
		return array(
			'd' => array(
				'name' => tr('Drop Down'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'help' => 'Drop Down - Radio Tracker Field',
				'prefs' => array('trackerfield_dropdown'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					),
				),
			),
			'D' => array(
				'name' => tr('Drop Down with Other field'),
				'description' => tr('Allows users to select from a specified set of options or to enter an alternate option'),
				'help' => 'Drop Down - Radio Tracker Field',
				'prefs' => array('trackerfield_dropdownother'),
				'tags' => array('basic'),
				'default' => 'n',
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label. It is recommended to add an "other" option.'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					),
				),
			),
			'R' => array(
				'name' => tr('Radio Buttons'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'help' => 'Drop Down - Radio Tracker Field',				
				'prefs' => array('trackerfield_radio'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					),
				),
			),
			'M' => array(
				'name' => tr('Multiselect'),
				'description' => tr('Allows a user to select multiple values from a specified set of options'),
				'help' => 'Multiselect Tracker Field',				
				'prefs' => array('trackerfield_multiselect'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					),
				),
			),
		);
	}

	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		return new Tracker_Field_Dropdown($fieldInfo, $itemData, $trackerDefinition);
	}
	
	function getFieldData(array $requestData = array())
	{
		
		$ins_id = $this->getInsertId();

		if (!empty($requestData['other_'.$this->getInsertId()])) {
			$value = $requestData['other_'.$this->getInsertId()];
		} elseif (isset($requestData[$this->getInsertId()])) {
			$value = implode(',', (array) $requestData[$this->getInsertId()]);
		} else {
			$value = $this->getValue($this->getDefaultValue());
		}

		return array(
			'value' => $value,
			'selected' => explode(',', $value),
			'possibilities' => $this->getPossibilities(),
		);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/dropdown.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		if ($context['list_mode'] === 'csv') {
			return implode(', ', $this->getConfiguration('selected'));
		} else {
			$labels = array_map(array($this, 'getValueLabel'), $this->getConfiguration('selected'));
			return implode(', ', $labels);
		}
	}

	private function getValueLabel($value)
	{
		$possibilities = $this->getConfiguration('possibilities');
		if (isset($possibilities[$value])) {
			return $possibilities[$value];
		} else {
			return $value;
		}
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

	private function getPossibilities()
	{
		$options = $this->getOption('options');

		if (empty($options)) {
			return array();
		}

		$out = array();
		foreach ($options as $value) {
			$out[$this->getValuePortion($value)] = $this->getLabelPortion($value);
		}

		return $out;
	}
	
	private function getDefaultValue()
	{
		$options = $this->getOption('options');
		
		$parts = array();
		$last = false;
		foreach ($options as $opt) {
			if ($last === $opt) {
				$parts[] = $this->getValuePortion($opt);
			}

			$last = $opt;
		}

		return implode(',', $parts);
	}

	private function getValuePortion($value)
	{
		if (false === $pos = strpos($value, '=')) {
			return $value;
		} else {
			return substr($value, 0, $pos);
		}
	}

	private function getLabelPortion($value)
	{
		if (false === $pos = strpos($value, '=')) {
			return $value;
		} else {
			return substr($value, $pos + 1);
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$value = $this->getValue();
		$label = $this->getValueLabel($value);
		$baseKey = $this->getBaseKey();

		return array(
			$baseKey => $typeFactory->identifier($value),
			"{$baseKey}_text" => $typeFactory->sortable($label),
		);
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return array($baseKey, $baseKey . '_text');
	}

	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();
		return array("{$baseKey}_text" => true);
	}

	function getFacets()
	{
		$baseKey = $this->getBaseKey();
		return array(
			Search_Query_Facet_Term::fromField($baseKey)
				->setLabel($this->getConfiguration('name'))
				->setRenderMap($this->getPossibilities())
		);
	}
}

