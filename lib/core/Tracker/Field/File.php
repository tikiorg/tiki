<?php

/**
 * Handler class for File
 * 
 * Letter key: ~A~
 *
 */
class Tracker_Field_File extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array('value' => '');
		if (isset($_FILES[$ins_id]) && is_uploaded_file($_FILES[$ins_id]['tmp_name'])) {
			$data['value'] = file_get_contents($_FILES[$ins_id]['tmp_name']);
			$data['file_type'] = $_FILES[$ins_id]['type'];
			$data['file_size'] = $_FILES[$ins_id]['size'];
			$data['file_name'] = $_FILES[$ins_id]['name'];
		}
		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/file.tpl', $context);
	}
}

