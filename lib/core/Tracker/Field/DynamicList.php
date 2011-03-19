<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_DynamicList extends Tracker_Field_Abstract
{
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
		
		TikiLib::lib('header')->add_jq_onready('
$("select[name=ins_' . $this->getOption(2) . ']").change(function(e, val) {
	$.getJSON(
		"tiki-tracker_http_request.php",
		{
			trackerIdList: ' . $this->getOption(0) . ',
			fieldlist: ' . $this->getOption(3) . ',
			filterfield: ' . $this->getOption(1) . ',
			status: "' . $this->getOption(4) . '",
			mandatory: "' . $this->getConfiguration('isMandatory') . '",
			filtervalue: $(this).val()
		},
		function(data, status) {
			$ddl = $("select[name=' . $this->getInsertId() . ']");
			$ddl.empty();
			$.each( data, function (i,v) {
				$ddl.append(
					$("<option value=" + v + ">" + v + "</option>")
				);					
			});
			if (val) {
				$ddl.val(val);
			}
		}
	);
}).trigger("change", ["' . $this->getValue() . '"]);
		');
		
		return '<select name="' . $this->getInsertId() . '"></select>';
	
	}
}

