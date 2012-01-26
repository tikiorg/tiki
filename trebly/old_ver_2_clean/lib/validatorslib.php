<?php 
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Validators
{
	private $input;

	function __construct() {
		global $prefs;
		$this->available = $this->get_all_validators();
	}
	
	function setInput($input) {
		$this->input = $input;
		return true;
	}
	
	function getInput() {
		if (isset($this->input)) {
			return $this->input;		
		} else {
			return false;
		}
	}
	
	function validateInput( $validator, $parameter = '', $message = '' ) {
		include_once('lib/validators/validator_' . $validator . '.php');
		if (!function_exists("validator_$validator") || !isset($this->input)) {
			return false;
		}
		$func_name = "validator_$validator";
		$result = $func_name($this->input, $parameter, $message);
		return $result;
	}
	
	private function get_all_validators() {
		$validators = array();
		foreach( glob( 'lib/validators/validator_*.php' ) as $file ) {
			$base = basename( $file );
			$validator = substr( $base, 10, -4 );
			$validators[] = $validator;
		}
		return $validators;
	}
	
	function generateTrackerValidateJS( $fields_data, $prefix = "ins_", $custom_rules = '', $custom_messages = '' ) {
		$validationjs = 'rules: { ';
		foreach ($fields_data as $field_value) {
			if ($field_value['validation'] || $field_value['isMandatory'] == 'y') {				
				$validationjs .= $prefix . $field_value['fieldId'] . ': { ';
				if ($field_value['isMandatory'] == 'y') {
					$validationjs .= 'required: true, ';		
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
						$validationjs .= 'parameter: "' .$field_value['validationParam'].'", ';
					}
					$validationjs .= 'message: "' .$field_value['validationMessage'].'", ';
					$validationjs .= 'input: function() { ';
					if ( $prefix == 'ins_' && $field_value['type'] == 'a') {
						$validationjs .= 'return $("#area_'.$field_value['fieldId'].'").val(); ';
					} else {
						$validationjs .= 'return $("#'.$prefix.$field_value['fieldId'].'").val(); ';
					}
					$validationjs .= '} } } ';
				} else {
					// remove last comma (not supported in IE7)
                			$validationjs = rtrim($validationjs, " ,");
				}
				$validationjs .= '}, ';
			}
		}
		$validationjs .= $custom_rules;
		// remove last comma (not supported in IE7)
		$validationjs = rtrim($validationjs, " ,"); 
		$validationjs .= '}, ';
		$validationjs .= 'messages: { ';
		foreach ($fields_data as $field_value) {
			if ($field_value['validationMessage'] && $field_value['isMandatory'] == 'y') {
				$validationjs .= $prefix . $field_value['fieldId'] . ': { ';
				$validationjs .= 'required: "' .$field_value['validationMessage'].'" ';
				$validationjs .= '}, ';	
			}
		}
		$validationjs .= $custom_messages;
		// remove last comma (not supported in IE7)
                $validationjs = rtrim($validationjs, " ,");
		$validationjs .= '} ';		
		return $validationjs;
	}
}

global $validatorslib;
$validatorslib = new Validators;
