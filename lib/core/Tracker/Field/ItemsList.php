<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for ItemsList
 *
 * Letter key: ~l~
 *
 */
class Tracker_Field_ItemsList extends Tracker_Field_Abstract implements Tracker_Field_Exportable
{
	public static function getTypes()
	{
		return array(
			'l' => array(
				'name' => tr('Items List'),
				'description' => tr('Displays a list of field values from another tracker that has a relation with this tracker.'),
				'readonly' => true,
				'help' => 'Items List and Item Link Tracker Fields',
				'prefs' => array('trackerfield_itemslist'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'trackerId' => array(
						'name' => tr('Tracker ID'),
						'description' => tr('Tracker from which to list items'),
						'filter' => 'int',
						'legacy_index' => 0,
						'profile_reference' => 'tracker',
					),
					'fieldIdThere' => array(
						'name' => tr('Link Field ID'),
						'description' => tr('Field ID from the other tracker containing an item link pointing to the item in this tracker or some other value to be matched.'),
						'filter' => 'int',
						'legacy_index' => 1,
						'profile_reference' => 'tracker_field',
						'parent' => 'trackerId',
						'parentkey' => 'tracker_id',
						'sort_order' => 'position_nasc',
					),
					'fieldIdHere' => array(
						'name' => tr('Value Field ID'),
						'description' => tr('Field ID from this tracker matching the value in the link field ID from the other tracker if the field above is not an item link.'),
						'filter' => 'int',
						'legacy_index' => 2,
						'profile_reference' => 'tracker_field',
						'parent' => 'input[name=trackerId]',
						'parentkey' => 'tracker_id',
						'sort_order' => 'position_nasc',
					),
					'displayFieldIdThere' => array(
						'name' => tr('Fields to display'),
						'description' => tr('Display alternate fields from the other tracker instead of the item title'),
						'filter' => 'int',
						'separator' => '|',
						'legacy_index' => 3,
						'profile_reference' => 'tracker_field',
						'parent' => 'trackerId',
						'parentkey' => 'tracker_id',
						'sort_order' => 'position_nasc',
					),
					'displayFieldIdThereFormat' => array(
						'name' => tr('Format for customising fields to display'),
						'description' => tr('Uses the translate function to replace %0 etc with the field values. E.g. "%0 any text %1"'),
						'filter' => 'text',
					),
					'linkToItems' => array(
						'name' => tr('Display'),
						'description' => tr('How the link to the items should be rendered'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Value'),
							1 => tr('Link'),
						),
						'legacy_index' => 4,
					),
					'status' => array(
						'name' => tr('Status Filter'),
						'description' => tr('Limit the available items to a selected set'),
						'filter' => 'alpha',
						'options' => array(
							'opc' => tr('all'),
							'o' => tr('open'),
							'p' => tr('pending'),
							'c' => tr('closed'),
							'op' => tr('open, pending'),
							'pc' => tr('pending, closed'),
						),
						'legacy_index' => 5,
					),
				),
			),
		);
	}

	
	/**
	 * Get field data
	 * @see Tracker_Field_Interface::getFieldData()
	 * 
	 */
	function getFieldData(array $requestData = array())
	{
		$items = $this->getItemIds();
		$list = $this->getItemLabels($items);
		
		$ret = array(
			'value' => '',
			'items' => $list,
		);
		
		return $ret;
	}

	function renderInput($context = array())
	{
		if( empty($this->getOption('fieldIdHere')) ) {
			return $this->renderOutput();
		} else {
			TikiLib::lib('header')->add_jq_onready(
				'
$("input[name=ins_' . $this->getOption('fieldIdHere') . '], select[name=ins_' . $this->getOption('fieldIdHere') . ']").change(function(e) {
	$.getJSON(
		"tiki-ajax_services.php",
		{
			controller: "tracker",
			action: "itemslist_output",
			field: "' . $this->getConfiguration('fieldId') . '",
			fieldIdHere: "' . $this->getOption('fieldIdHere') . '",
			value: $(this).val()
		},
		function(data, status) {
			$ddl = $("div[name=' . $this->getInsertId() . ']");
			$ddl.html(data);
			if (jqueryTiki.chosen) {
				$ddl.trigger("chosen:updated");
			}
			$ddl.trigger("change");
		}
	);
}).trigger("change");
			'
			);
			return '<div name="' . $this->getInsertId() . '"></div>';
		}
	}

	function renderOutput( $context = array() )
	{
		if (isset($context['search_render']) && $context['search_render'] == 'y') {
			$items = $this->getData($this->getConfiguration('fieldId'));
		} else {
			$items = $this->getItemIds();
		}

		$list = $this->getItemLabels($items, $context);

		// if nothing found check definition for previous list (used for output render)
		if (empty($list)) {
			$list = $this->getConfiguration('items', array());
			$items = array_keys($list);
		}

		if ($context['list_mode'] === 'csv') {
			return implode('%%%', $list);
		} else {
			return $this->renderTemplate(
				'trackeroutput/itemslist.tpl',
				$context,
				array(
					'links' => (bool) $this->getOption('linkToItems'),
					'raw' => (bool) $this->getOption('displayFieldIdThere'),
					'itemIds' => implode(',', $items),
					'items' => $list,
					'num' => count($list),
				)
			);
		}
	}

	function watchCompare($old, $new)
	{
		$o = '';
		$items = $this->getItemIds();
		$n = $this->getItemLabels($items);

		return parent::watchCompare($o, $n);	// then compare as text
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		$items = $this->getItemIds();

		$list = $this->getItemLabels($items);
		$listtext = implode(' ', $list);

		return array(
			$baseKey => $typeFactory->multivalue($items),
			"{$baseKey}_text" => $typeFactory->sortable($listtext),
		);
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return array(
			$baseKey,
			"{$baseKey}_text",
		);
	}

	function getGlobalFields()
	{
		return array();
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());
		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');

		$schema->addNew($permName, 'multi-id')
			->setLabel($name)
			->setReadOnly(true)
			->setRenderTransform(function ($value) {
				return implode(';', $value);
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			});

		$schema->addNew($permName, 'multi-name')
			->setLabel($name)
			->setReadOnly(true)
			->setRenderTransform(function ($value, $extra) {

				if (is_string($value) && empty($value)) {
					// ItemsLists have no stored value, so when called from \Tracker\Tabular\Source\TrackerSourceEntry...
					// we have to: get a copy of this field
					$field = $this->getTrackerDefinition()->getFieldFromPermName($this->getConfiguration('permName'));
					// get a new handler for it
					$factory = $this->getTrackerDefinition()->getFieldFactory();
					$handler = $factory->getHandler($field, ['itemId' => $extra['itemId']]);
					// for which we can then get the itemIds array of the "linked" items
					$value = $handler->getItemIds();
					// and then get the labels from the id's we've now found as if they were the field's data
				}

				$labels = $this->getItemLabels($value, ['list_mode' => 'csv']);
				return implode(';', $labels);
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			});


		return $schema;
	}



	private function getItemIds()
	{
		$trklib = TikiLib::lib('trk');
		$trackerId = (int) $this->getOption('trackerId');
		
		$filterFieldIdHere = (int) $this->getOption('fieldIdHere');
		$filterFieldIdThere = (int) $this->getOption('fieldIdThere');
		
		$filterFieldHere = $this->getTrackerDefinition()->getField($filterFieldIdHere); 
		$filterFieldThere = $trklib->get_tracker_field($filterFieldIdThere);
		
		$displayFieldIds = $this->getOption('displayFieldIdThere');
		$status = $this->getOption('status', 'opc');
		$tracker = Tracker_Definition::get($trackerId);

		// note: if itemlink or dynamic item list is used, than the final value to compare with must be calculated based on the current itemid

		$technique = 'value';

		// not sure this is working
		// r = item link
		if ($tracker && $filterFieldThere && (!$filterFieldIdHere || $filterFieldThere['type'] === 'r')) {
			if ($filterFieldThere['type'] == 'r') {
				$technique = 'id';
			}
		}
		
		// not sure this is working
		// q = Autoincrement
		if ($filterFieldHere['type'] == 'q' && isset($filterFieldHere['options_array'][3]) && $filterFieldHere['options_array'][3] == 'itemId') {
			$technique = 'id';
		}

		if ($technique == 'id') {
			$itemId = $this->getItemId();
			if( !$itemId ) {
				$items = array();
			} else {
				$items = $trklib->get_items_list($trackerId, $filterFieldIdThere, $itemId, $status);
			}
		} else {
			// when this is an item link or dynamic item list field, localvalue contains the target itemId
			$localValue = $this->getData($filterFieldIdHere); 
			if (!$localValue) {
				// in some cases e.g. pretty tracker $this->getData($filterFieldIdHere) is not reliable as the info is not there
				// Note: this fix only works if the itemId is passed via the template
				$itemId = $this->getItemId();
				$localValue = $trklib->get_item_value($trackerId, $itemId, $filterFieldIdHere);
			}
			// r = item link - not sure this is working 
			if ($filterFieldHere['type'] == 'r' && isset($filterFieldHere['options_array'][0]) && isset($filterFieldHere['options_array'][1])) {
				$localValue = $trklib->get_item_value($filterFieldHere['options_array'][0], $localValue, $filterFieldHere['options_array'][1]);
			}
			
			// w = dynamic item list - localvalue is the itemid of the target item. so rewrite.
			if ($filterFieldHere['type'] == 'w') {
				$localValue = $trklib->get_item_value($trackerId, $localValue, $filterFieldIdThere);
			}
			// Skip nulls
			if ($localValue) {
				$items = $trklib->get_items_list($trackerId, $filterFieldIdThere, $localValue, $status);
			} else {
				$items = array();
			}
		}

		return $items;
	}

	/**
	 * Get value of displayfields from given array of itemIds
	 * @param array $items
	 * @param array $context
	 * @return array array of values by itemId
	 */
	private function getItemLabels($items, $context = array('list_mode' => ''))
	{
		$displayFields = $this->getOption('displayFieldIdThere');
		$trackerId = (int) $this->getOption('trackerId');
		$status = $this->getOption('status', 'opc');

		$definition = Tracker_Definition::get($trackerId);
		if (! $definition) {
			return array();
		}

		$list = array();
		$trklib = TikiLib::lib('trk');
		foreach ($items as $itemId) {
			if ($displayFields && $displayFields[0]) {
				$list[$itemId] = $trklib->concat_item_from_fieldslist(
					$trackerId,
					$itemId,
					$displayFields,
					$status,
					' ',
					$context['list_mode'],
					$this->getOption('linkToItems'),
					$this->getOption('displayFieldIdThereFormat')
				);
			} else {
				$list[$itemId] = $trklib->get_isMain_value($trackerId, $itemId);
			}
		}

		return $list;
	}
}

