<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

		if (isset($_FILES[$ins_id]) && is_uploaded_file($_FILES[$ins_id]['tmp_name'])) {
			$data['old_value'] = $this->getValue();
			$data['value'] = file_get_contents($_FILES[$ins_id]['tmp_name']);
			$data['file_type'] = $_FILES[$ins_id]['type'];
			$data['file_size'] = $_FILES[$ins_id]['size'];
			$data['file_name'] = $_FILES[$ins_id]['name'];
		} else {
			$data = array('value' => $this->getValue());
		}
		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/file.tpl', $context);
	}
	
	function renderInnerOutput( $context ) {
		$list = TikiLib::lib('trk')->list_item_attachments($this->getItemId(), 0, -1, 'attId_asc');
		if (empty($list['data'])) {
			return '';
		} else if (count($list['data']) === 1) {
			return $list['data'][0]['filename'];
		} else {
			$ul = '<ul>';
			foreach ( $list['data'] as $item ) {
				$ul .= '<li>' . $item['filename'] . '</li>';
			}
			$ul .= '</ul>';
			return $ul;
		}
	}
}

