<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Freetags
 *
 * Letter key: ~F~
 *
 */
class Tracker_Field_Freetags extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'F' => array(
				'name' => tr('Tags'),
				'description' => tr('Allows tags to be shown or added for tracker items.'),
				'prefs' => array('trackerfield_freetags', 'feature_freetags'),
				'tags' => array('advanced'),
				'default' => 'y',
				'params' => array(
					'size' => array(
						'name' => tr('Size'),
						'description' => tr('Visible size of the input field'),
						'filter' => 'int',
						'legacy_index' => 0,
					),
					'hidehelp' => array(
						'name' => tr('Help'),
						'description' => tr('Hide or show the input help'),
						'default' => '',
						'filter' => 'alpha',
						'options' => array(
							'' => tr('Show'),
							'y' => tr('Hide'),
						),
						'legacy_index' => 1,
					),
					'hidesuggest' => array(
						'name' => tr('Suggest'),
						'description' => tr('Hide or show the tag suggestions'),
						'default' => '',
						'filter' => 'alpha',
						'options' => array(
							'' => tr('Show'),
							'y' => tr('Hide'),
						),
						'legacy_index' => 2,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$data = array();

		$ins_id = $this->getInsertId();

		if (isset($requestData[$ins_id])) {
			$data['value'] = $requestData[$ins_id];
		} else {
			global $prefs;

			$data['value'] = $this->getValue();

			$langutil = new Services_Language_Utilities;
			$itemLang = null;
			if ($this->getItemId()) {
				try {
					$itemLang = $langutil->getLanguage('trackeritem', $this->getItemId());
				} catch (Services_Exception $e) {
					$itemLang = null;
				}
			}
			$freetaglib = TikiLib::lib('freetag');
			$data['freetags'] = $freetaglib->_parse_tag($data['value']);
			$data['tag_suggestion'] = $freetaglib->get_tag_suggestion(
				implode(' ', $data['freetags']),
				$prefs['freetags_browse_amount_tags_suggestion'],
				$itemLang
			);
		}

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/freetags.tpl', $context);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/freetags.tpl', $context);
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
}

