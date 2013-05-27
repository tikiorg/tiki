<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for DynamicList
 *
 * Letter key: ~w~
 *
 */
// TODO: validate parameters (several required)
class Tracker_Field_DynamicList extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'w' => array(
				'name' => tra('Dynamic Items List'),
				'description' => tra('Dynamically updates a selection list based on linked data from another tracker.'),
				'help' => 'Dynamic items list',
				'prefs' => array('trackerfield_dynamiclist'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'trackerId' => array(
						'name' => tr('Tracker ID'),
						'description' => tr('Tracker to link with'),
						'filter' => 'int',
					),
					'filterFieldIdThere' => array(
						'name' => tr('Field ID (Other tracker)'),
						'description' => tr('Field ID to link with in the other tracker'),
						'filter' => 'int',
					),
					'filterFieldIdHere' => array(
						'name' => tr('Field ID (This tracker)'),
						'description' => tr('Field ID to link with in the current tracker'),
						'filter' => 'int',
					),
					'listFieldIdThere' => array(
						'name' => tr('Listed Field'),
						'description' => tr('Field ID to be displayed in the drop list.'),
						'filter' => 'int',
					),
					'statusThere' => array(
						'name' => tr('Status Filter'),
						'description' => tr('Restrict listed items to specific statuses.'),
						'filter' => 'alpha',
						'options' => array(
							'opc' => tr('all'),
							'o' => tr('open'),
							'p' => tr('pending'),
							'c' => tr('closed'),
							'op' => tr('open, pending'),
							'pc' => tr('pending, closed'),
						),
					),
				),
			),
		);
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
		// REFACTOR: can't use list-tracker_field_values_ajax.php yet as it doesn't seem to filter

		TikiLib::lib('header')->add_jq_onready(
			'
$("select[name=ins_' . $this->getOption('filterFieldIdHere') . ']").change(function(e, val) {
	$.getJSON(
		"tiki-tracker_http_request.php",
		{
			trackerIdList: ' . $this->getOption('trackerId') . ',
			fieldlist: ' . $this->getOption('listFieldIdThere') . ',
			filterfield: ' . $this->getOption('filterFieldIdThere') . ',
			status: "' . $this->getOption('statusThere') . '",
			mandatory: "' . $this->getConfiguration('isMandatory') . '",
			item: $(this).val() // We need the field value for the fieldId filterfield for the item $(this).val
		},
		function(data, status) {
			$ddl = $("select[name=' . $this->getInsertId() . ']");
			$ddl.empty();
			var v, l;
			if (data) {
				$.each( data, function (i,data) {
					if (data && data.length > 1) {
						v = data[0];
						l = data[1];
					} else {
						v = ""
						l = "";
					}
					$ddl.append(
						$("<option/>")
							.val(v)
							.text(l)
					);
				});
				if (val) {
					$ddl.val(val);
				}
			}
		}
	);
}).trigger("change", ["' . $this->getConfiguration('value') . '"]);
		'
		);

		return '<select name="' . $this->getInsertId() . '"></select>';

	}

	public function renderInnerOutput($context = array()) {

		$definition = Tracker_Definition::get($this->getOption('trackerId'));
		$field = $definition->getField($this->getOption('listFieldIdThere'));

		if ($field['type'] === 'e') {
			$item = $this->getItemData();
			$item['ins_' . $this->getOption('listFieldIdThere')] = array($this->getValue());
			$field['value'] = $this->getValue();

			$handler = TikiLib::lib('trk')->get_field_handler($field, $item);

			$field = array_merge($field, $handler->getFieldData($item));	// get category field to build it's data arrays
			$handler = TikiLib::lib('trk')->get_field_handler($field, $item);

			return $handler->renderOutput($context);
		} else {
			return parent::renderInnerOutput($context);
		}
	}

}

