<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Validators
{
	private $input;

	function __construct()
	{
		global $prefs;
		$this->available = $this->get_all_validators();
	}

	function setInput($input)
	{
		$this->input = $input;
		return true;
	}

	function getInput()
	{
		if (isset($this->input)) {
			return $this->input;
		} else {
			return false;
		}
	}

	function validateInput( $validator, $parameter = '', $message = '' )
	{
		include_once('lib/validators/validator_' . $validator . '.php');
		if (!function_exists("validator_$validator") || !isset($this->input)) {
			return false;
		}
		$func_name = "validator_$validator";
		$result = $func_name($this->input, $parameter, $message);
		return $result;
	}

	private function get_all_validators()
	{
		$validators = array();
		foreach ( glob('lib/validators/validator_*.php') as $file ) {
			$base = basename($file);
			$validator = substr($base, 10, -4);
			$validators[] = $validator;
		}
		return $validators;
	}

	function generateTrackerValidateJS( $fields_data, $prefix = 'ins_', $custom_rules = '', $custom_messages = '' )
	{
		$validationjs = 'rules: { ';
		foreach ($fields_data as $field_value) {
			if ($field_value['validation'] || $field_value['isMandatory'] == 'y') {
				if ($field_value['type'] == 'e' || $field_value['type'] == 'M') {
					$validationjs .= '"' . $prefix . $field_value['fieldId'] . '[]": { ';
				} else {
					$validationjs .= $prefix . $field_value['fieldId'] . ': { ';
				}
				if ($field_value['isMandatory'] == 'y') {
					if ($field_value['type'] == 'D') {
						$validationjs .= 'required_in_group: [1, ".group_'.$prefix.$field_value['fieldId'].'", "other"], ';
					} else if ($field_value['type'] == 'A') {
						$validationjs .= 'required_tracker_file: [1, ".file_'.$prefix.$field_value['fieldId'].'"], ';
					} else if ($field_value['type'] == 'f') {	// old style date picker - jq validator rules have to apply to an element name or id
																// so we have to add a required_in_group for each of the date selects in turn
						$validationjs .= 'required: false },';	// dummy for the "group"
						$date_ins_num = $field_value['options_array'][0] === 'dt' ? 5 : 3;
						$validationjs .= $prefix . $field_value['fieldId'] . 'Month: {required_in_group: [' . $date_ins_num . ', "select[name^='.$prefix.$field_value['fieldId'].']"]}, ' .
							$prefix . $field_value['fieldId'] . 'Day: {required_in_group: [' . $date_ins_num . ', "select[name^='.$prefix.$field_value['fieldId'].']"]}, ' .
							$prefix . $field_value['fieldId'] . 'Year: {required_in_group: [' . $date_ins_num . ', "select[name^='.$prefix.$field_value['fieldId'].']"], ';
						if ($field_value['options_array'][0] === 'dt') {
							$validationjs = rtrim($validationjs, ', ');
							$validationjs .= '},';
							$validationjs .= $prefix . $field_value['fieldId'] . 'Hour: {required_in_group: [' . $date_ins_num . ', "select[name^='.$prefix.$field_value['fieldId'].']"]}, ' .
								$prefix . $field_value['fieldId'] . 'Minute: {required_in_group: [' . $date_ins_num . ', "select[name^='.$prefix.$field_value['fieldId'].']"], ';
						}
					} else {
						$validationjs .= 'required: true, ';
					}
				}
				if ($field_value['validation']) {
					$validationjs .= 'remote: { ';
					$validationjs .= 'url: "validate-ajax.php", ';
					$validationjs .= 'type: "post", ';
					$validationjs .= 'data: { ';
					$validationjs .= 'validator: "' .$field_value['validation'].'", ';
					if ($field_value['validation'] == 'distinct' && empty($field_value['validationParam'])) {
						if (isset($_REQUEST['itemId']) && $_REQUEST['itemId'] > 0) {
							$current_id = $_REQUEST['itemId'];
						} else {
							$current_id = 0;
						}
						$validationjs .= 'parameter: "trackerId=' .$field_value['trackerId'].'&fieldId=' .$field_value['fieldId'] . '&itemId=' . $current_id . '", ';
					} else {
						$validationjs .= 'parameter: "' .addslashes($field_value['validationParam']).'", ';
					}
					$validationjs .= 'message: "' .tra($field_value['validationMessage']).'", ';
					$validationjs .= 'input: function() { ';
					if ( $prefix == 'ins_' && $field_value['type'] == 'a') {
						$validationjs .= 'return $("#area_'.$field_value['fieldId'].'").val(); ';
					} elseif ( $prefix == 'ins_' && $field_value['type'] == 'k') {
						$validationjs .= 'return $("#page_selector_'.$field_value['fieldId'].'").val(); ';
					} else {
						if ( $field_value['type'] == 'g' or $field_value['type'] == 'e' or $field_value['type'] == 'y' or $field_value['type'] == 'd' or $field_value['type'] == 'D' ) {
							// Let's handle drop-down style fields
							$validationjs .= 'return $(\'select[name="'.$prefix.$field_value['fieldId'].'"] option:selected\').text(); ';
						} else {	// Let's handle text style fields
							$validationjs .= 'return $("#'.$prefix.$field_value['fieldId'].'").val(); ';
						}
					}
					$validationjs .= '} } } ';
				} else {
					// remove last comma (not supported in IE7)
					$validationjs = rtrim($validationjs, ' ,');
				}
				$validationjs .= '}, ';
			}
		}
		$validationjs .= $custom_rules;
		// remove last comma (not supported in IE7)
		$validationjs = rtrim($validationjs, ' ,');
		$validationjs .= '}, ';
		$validationjs .= 'messages: { ';
		foreach ($fields_data as $field_value) {
			if ($field_value['validationMessage'] && $field_value['isMandatory'] == 'y') {
				$validationjs .= $prefix . $field_value['fieldId'] . ': { ';
				$validationjs .= 'required: "' .tra($field_value['validationMessage']).'" ';
				$validationjs .= '}, ';
			} elseif ($field_value['isMandatory'] == 'y') {
				$validationjs .= $prefix . $field_value['fieldId'] . ': { ';
				$validationjs .= 'required: "' .tra('This field is required').'" ';
				$validationjs .= '}, ';
			}
		}
		$validationjs .= $custom_messages;
		// remove last comma (not supported in IE7)
		$validationjs = rtrim($validationjs, ' ,');
		$validationjs .= '}, ';
		// Add an invalidHandler to scroll the first error into view
		// works in both modal and full page modes and leaves the focus on the error input
		$validationjs .= '
focusInvalid: false,
invalidHandler: function(event, validator) {
	var errors = validator.numberOfInvalids();
	if (errors) {
		var $container = $(this).parents(".modal");
		if (!$container.length) {
			$container = $("html, body");
		}
		var containerScrollTop = $container.scrollTop(),
			$firstError = $(validator.errorList[0].element),
			$scrollElement = $firstError.parents(".form-group");

		if (! $scrollElement.length) {
			$scrollElement = $firstError;
		}
		$container.animate({
			scrollTop: containerScrollTop + $scrollElement.offset().top
		}, 1000, function () {
			$firstError.focus();
		});
	}
}';
		return $validationjs;
	}
}

