<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	public static function getTypes()
	{
		return array(
			'A' => array(
				'name' => tr('Attachment'),
				'description' => tr('Deprecated in favor of the Files field.'),
				'help' => 'Attachment Field',
				'prefs' => array('trackerfield_file'),
				'tags' => array('basic'),
				'default' => 'n',
				'params' => array(
					'listview' => array(
						'name' => tr('List View'),
						'description' => tr('Defines how attachments will be displayed within the field.'),
						'options' => array(
							'n' => tr('name'),
							't' => tr('type'),
							'ns' => tr('name, size'),
							'nts' => tr('name, type, size'),
							'u' => tr('uploader'),
							'm' => tr('mediaplayer'),
						),
						'legacy_index' => 0,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		if (!empty($requestData) && isset($_FILES[$ins_id]) && is_uploaded_file($_FILES[$ins_id]['tmp_name'])) {
			$data['old_value'] = $this->getValue();
			$data['value'] = file_get_contents($_FILES[$ins_id]['tmp_name']);
			$data['file_type'] = $_FILES[$ins_id]['type'];
			$data['file_size'] = $_FILES[$ins_id]['size'];
			$data['file_name'] = $_FILES[$ins_id]['name'];
		} else {
			$data = array('value' => $this->getValue());
			if (!empty($data['value']) && (int) $data['value'] > 0) {
				$attachment = TikiLib::lib('trk')->get_item_attachment($data['value']);
				$data['filename'] = $attachment['filename'];
			}
		}
		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/file.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		$att_id = $this->getValue();

		if (empty($att_id)) {
			return '';
		}

		if ($context['list_mode'] === 'csv') {
			global $base_url;
			return $base_url . 'tiki-download_item_attachment.php?attId=' . $att_id;	// should something to do with export_attachment() happen here?
		}

		$attachment = TikiLib::lib('trk')->get_item_attachment($att_id);

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_block_self_link');
		$smarty->loadPlugin('smarty_function_icon');

		$link = smarty_block_self_link(
			array(
				'_script' => 'tiki-download_item_attachment.php',
				'attId' => $att_id,
			),
			smarty_function_icon(array('_id' => 'disk', 'alt' => tra('Download')), $smarty) . ' ' .
			$attachment['filename'],
			$smarty
		);
		return $link;
	}

	function handleSave($value, $oldValue)
	{
		global $prefs, $user;
		$tikilib = TikiLib::lib('tiki');

		$trackerId = $this->getConfiguration('trackerId');
		$file_name = $this->getConfiguration('file_name');
		$file_size = $this->getConfiguration('file_size');
		$file_type = $this->getConfiguration('file_type');

		$perms = Perms::get('tracker', $trackerId);
		if ($perms->attach_trackers && $file_name) {
			if ($prefs['t_use_db'] == 'n') {
				$fhash = md5($file_name.$tikilib->now);
				if (file_put_contents($prefs['t_use_dir'] . $fhash, $value) === false) {
					$smarty = TikiLib::lib('smarty');
					$smarty->assign('msg', tra('Cannot write to this file:'). $fhash);
					$smarty->display("error.tpl");
					die;
				}
				$value = '';
			} else {
				$fhash = 0;
			}

			$trklib = TikiLib::lib('trk');
			$value = $trklib->replace_item_attachment($oldValue, $file_name, $file_type, $file_size, $value, '', $user, $fhash, '', '', $trackerId, $this->getItemId(), '', false);
		}

		return array(
			'value' => $value,
		);
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		return array(
		);
	}

	function getProvidedFields()
	{
		return array();
	}

	function getGlobalFields()
	{
		return array();
	}
}

