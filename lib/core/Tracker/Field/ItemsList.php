<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_ItemsList extends Tracker_Field_Abstract
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
						'description' => tr('Tracker to list items from'),
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
					),
					'fieldIdHere' => array(
						'name' => tr('Value Field ID'),
						'description' => tr('Field ID from this tracker matching the value in the link field ID from the other tracker if the field above is not an item link.'),
						'filter' => 'int',
						'legacy_index' => 2,
						'profile_reference' => 'tracker_field',
					),
					'displayFieldIdThere' => array(
						'name' => tr('Fields to display'),
						'description' => tr('Display alternate fields from the other tracker instead of the item title'),
						'filter' => 'int',
						'separator' => '|',
						'legacy_index' => 3,
						'profile_reference' => 'tracker_field',
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
		TikiLib::lib('header')->add_jq_onready(
			'
$("input[name=ins_' . $this->getOption('fieldIdHere') . '], select[name=ins_' . $this->getOption('fieldIdHere') . ']").change(function(e, val) {
  $.getJSON(
    "tiki-tracker_http_request_itemslist.php",
    {
      trackerIdList: ' . $this->getOption('trackerId') . ',
      fieldlist: "' . implode('|',$this->getOption('displayFieldIdThere')) . '",
      filterfield: ' . $this->getOption('fieldIdThere') . ',
      filtervalue: $(this).find("option:selected").val() ,
			status: "' . $this->getOption('status') . '",
			mandatory: "' . $this->getConfiguration('isMandatory') . '"
    },
    function(data, status) {
			$ddl = $("div[name=' . $this->getInsertId() . ']");
      $ddl.empty();
      var v, l;
      if (data) {
        $.each( data, function (i,data) {
          if (data && data.length > 1) {
            v = data[0];
            label = data[1];
          } else {
            v = "";
            label = "";
          }
          $ddl.append(
            $("<div class=\"tracker_field_itemslist tracker_field" + v + "\" />")
            .text(label)
          );
        });
      }
      if (jqueryTiki.chosen) {	// I only left this because I have no clue what it does
        $ddl.trigger("chosen:updated");
      }
      $ddl.trigger("change");
    }
  );
}).trigger("change", [""]);
		'
		);

		return '<div name="' . $this->getInsertId() . '"></div>';
	}

	function renderOutput( $context = array() )
	{
		if (isset($context['search_render']) && $context['search_render'] == 'y') {
			$items = $this->getData($this->getConfiguration('fieldId'));
		} else {
			$items = $this->getItemIds();
		}

		$list = $this->getItemLabels($items, $context);

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

	private function getItemIds()
	{
		$trackerId = (int) $this->getOption('trackerId');
		$remoteField = (int) $this->getOption('fieldIdThere');
		$localField = (int) $this->getOption('fieldIdHere');
		$localFieldDef = $this->getTrackerDefinition()->getField($localField);
		$displayFields = $this->getOption('displayFieldIdThere');
		$status = $this->getOption('status', 'opc');

		$tracker = Tracker_Definition::get($trackerId);
		$technique = 'value';

		if ($tracker && ($field = $tracker->getField($remoteField)) && (!$localField || $field['type'] === 'r')) {
			if ($field['type'] == 'r') {
				$technique = 'id';
			}
		}
		if ($localFieldDef['type'] == 'q' && isset($localFieldDef['options_array'][3]) && $localFieldDef['options_array'][3] == 'itemId') {		
			$technique = 'id';
		}

		$trklib = TikiLib::lib('trk');
		if ($technique == 'id') {
			$items = $trklib->get_items_list($trackerId, $remoteField, $this->getItemId(), $status);
		} else {
			$localValue = $this->getData($localField);
			if (!$localValue) {
				// in some cases e.g. pretty tracker $this->getData($localField) is not reliable as the info is not there
				// Note: this fix only works if the itemId is passed via the template
				$itemId = $this->getItemId();
				$localValue = $trklib->get_item_value($trackerId, $itemId, $localField);
			}
			if ($localFieldDef['type'] == 'r' && isset($localFieldDef['options_array'][0]) && isset($localFieldDef['options_array'][1])) {
				$localValue = $trklib->get_item_value($localFieldDef['options_array'][0], $localValue, $localFieldDef['options_array'][1]);
			}
			// Skip nulls
			if ($localValue) {
				$items = $trklib->get_items_list($trackerId, $remoteField, $localValue, $status);
			} else {
				$items = array();
			}
		}

		return $items;
	}

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
				$list[$itemId] = $trklib->concat_item_from_fieldslist($trackerId, $itemId, $displayFields, $status, ' ', $context['list_mode'], $this->getOption('linkToItems'));
			} else {
				$list[$itemId] = $trklib->get_isMain_value($trackerId, $itemId);
			}
		}

		return $list;
	}
}

