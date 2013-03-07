<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for ItemLink
 *
 * Letter key: ~r~
 *
 */
class Tracker_Field_ItemLink extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	const CASCADE_NONE = 0;
	const CASCADE_CATEG = 1;
	const CASCADE_STATUS = 2;
	const CASCADE_DELETE = 4;

	public static function getTypes()
	{
		return array(
			'r' => array(
				'name' => tr('Item Link'),
				'description' => tr('Link to an other item, similar to a foreign key.'),
				'help' => 'Items List and Item Link Tracker Fields',
				'prefs' => array('trackerfield_itemlink'),
				'tags' => array('advanced'),
				'default' => 'y',
				'params' => array(
					'trackerId' => array(
						'name' => tr('Tracker ID'),
						'description' => tr('Tracker to link to'),
						'filter' => 'int',
					),
					'fieldId' => array(
						'name' => tr('Field ID'),
						'description' => tr('Default field to display'),
						'filter' => 'int',
					),
					'linkToItem' => array(
						'name' => tr('Display'),
						'description' => tr('How the link to the item should be rendered'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Value'),
							1 => tr('Link'),
						),
					),
					'displayFieldsList' => array(
						'name' => tr('Multiple Fields'),
						'description' => tr('Display the values from multiple fields instead of a single one, separated by |'),
						'separator' => '|',
						'filter' => 'int',
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
					),
					'linkPage' => array(
						'name' => tr('Link Page'),
						'description' => tr('Link to a wiki page instead of directly to the item'),
						'filter' => 'pagename',
					),
					'addItems' => array(
						'name' => tr('Add Items'),
						'description' => tr('Display text to allow new items to be added - e.g. "Add item..." (requires jQuery-UI)'),
						'filter' => 'text',
					),
					'addItemsWikiTpl' => array(
						'name' => tr('Add Item Template Page'),
						'description' => tr('Wiki page to use as a Pretty Tracker template'),
						'filter' => 'pagename',
					),
					'preSelectFieldHere' => array(
						'name' => tr('Preselect item based on value in this field'),
						'description' => tr('Preselect item based on value in specified field ID of item being edited'),
						'filter' => 'int',
					),
					'preSelectFieldThere' => array(
						'name' => tr('Preselect based on value in this remote field'),
						'description' => tr('Match preselect item with this field ID in tracker that is being linked to'),
						'filter' => 'int',
					),
					'preSelectFieldMethod' => array(
						'name' => tr('Preselection matching method'),
						'description' => tr('Method to use to match fields for preselection purposes'),
						'filter' => 'alpha',
						'options' => array(
							'exact' => tr('Exact Match'),
							'partial' => tr('Field here is part of field there'),
							'domain' => tr('Match domain, used for URL fields'),
						),
					),
					'displayOneItem' => array(
						'name' => tr('One item per value'),
						'description' => tr('Display only one random item per value'),
						'filter' => 'alpha',
						'options' => array(
							'one' => tr('Only one random item for each value'),
							'multi' => tr('Displays all the items for a same value with a notation value (itemId)'),
						),
					),
					'selectMultipleValues' => array(
						'name' => tr('Select multiple values'),
						'description' => tr('Allow the user to select multiple values'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						)
					),
					'indexRemote' => array(
						'name' => tr('Index remote fields'),
						'description' => tr('Index one or multiple fields from the master tracker along with the child, separated by |'),
						'separator' => '|',
						'filter' => 'int',
					),
					'cascade' => array(
						'name' => tr('Cascade actions'),
						'description' => tr("Elements to cascade when the master is updated or deleted. Categories may conflict if multiple item links are used to different items attempting to manage the same categories. Same for status."),
						'filter' => 'int',
						'options' => array(
							self::CASCADE_NONE => tr('No'),
							self::CASCADE_CATEG => tr('Categories'),
							self::CASCADE_STATUS => tr('Status'),
							self::CASCADE_DELETE => tr('Delete'),
							(self::CASCADE_CATEG | self::CASCADE_STATUS) => tr('Categories and status'),
							(self::CASCADE_CATEG | self::CASCADE_DELETE) => tr('Categories and delete'),
							(self::CASCADE_DELETE | self::CASCADE_STATUS) => tr('Delete and status'),
							(self::CASCADE_CATEG | self::CASCADE_STATUS | self::CASCADE_DELETE) => tr('All'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$data = $this->getLinkData($requestData, $this->getInsertId());

		return $data;
	}

	function renderInput($context = array())
	{
		if ($this->getOption('addItems') && !$context['in_ajax_form']) {

			$context['in_ajax_form'] = true;

			require_once 'lib/wiki-plugins/wikiplugin_tracker.php';

			$params = array(
				'trackerId' => $this->getOption('trackerId'),
				'ignoreRequestItemId' => 'y',
				'_ajax_form_ins_id' => $this->getInsertId(),
			);

			if ($this->getOption('addItemsWikiTpl')) {
				$params['wiki'] = $this->getOption('addItemsWikiTpl');
			}
			$form = wikiplugin_tracker('', $params);

			$form = preg_replace(array('/<!--.*?-->/', '/\s+/', '/^~np~/', '/~\/np~/'), array('', ' ', '', ''), $form);	// remove comments etc

			if ($this->getOption('displayFieldsList')) {
				$displayFieldId = $this->getOption('displayFieldsList');
				if (strpos($displayFieldId, '|') !== false) {
					$displayFieldId = substr($displayFieldId, 0, strpos($displayFieldId, '|'));
				}
			} else {
				$displayFieldId = $this->getOption('fieldId');
			}

			TikiLib::lib('header')->add_jq_onready(
				'$("select[name=' . $this->getInsertId() . ']").change(function(e, val) {
	if ($(this).val() == -1) {
		var $d = $("<div id=\'add_dialog_' . $this->getInsertId() . '\' style=\'display:none\'>' . addslashes($form) . '</div>")
			.appendTo(document.body);

		var w = $d.width() * 1.4;
		var h = $d.height() * 2.0;
		if ($(document.body).width() < w) {
			w = $(document.body).width() * 0.8;
		}
		if ($(document.body).height() < h) {
			h = $(document.body).height() * 0.8;
		}

		$d.dialog({
				width: w,
				height: h,
				title: "'.$this->getOption('addItems').'",
				modal: true,
				buttons: {
					"Add": function() {
						var $f = $("form", this).append($("<input type=\'hidden\' name=\'ajax_add\' value=\'1\' />"));
						if (typeof $f.valid === "function" && $f.valid()) {
							ajaxLoadingShow($f);
							$.post( $f.attr("action"), $f.serialize(), function(data, status) {
								if (data && data.data) {
									for (var i = 0; i < data.data.length; i++) {
										var a = data.data[i];
										if ( a && a["fieldId"] == '. $displayFieldId .' ) {
											var $o = $("<option value=\'" + data["itemId"] + "\'>" + a["value"] + "</option>");
											$("select[name=' . $this->getInsertId() . '] > option:first").after($o);
											$("select[name=' . $this->getInsertId() . ']")[0].selectedIndex = 1;
										}
									}
								}
								ajaxLoadingHide();
								$d.dialog( "close" );

								return;
							}, "json");
						}
					},
					Cancel: function() {
						$("select[name=' . $this->getInsertId() . ']")[0].selectedIndex = 0;
						$( this ).dialog( "close" );
					}
				},
				create: function(event, ui) {
					 ajaxTrackerFormInit_' . $this->getInsertId() . '();
				}
			}).find(".input_submit_container").remove();
	}
});
'
);

		}

		$context['selectMultipleValues'] = (bool) $this->getOption('selectMultipleValues');

		if ($preselection = $this->getPreselection()) {
			$context['preselection'] = $preselection;
		} else {
			$context['preselection'] = '';
		}

		$context['filter'] = $this->buildFilter();

		return $this->renderTemplate('trackerinput/itemlink.tpl', $context);
	}

	private function buildFilter()
	{
		return array(
			'tracker_id' => $this->getOption('trackerId'),
		);
	}

	function renderOutput($context = array())
	{
		$smarty = TikiLib::lib('smarty');

		$item = $this->getValue();

		$dlist = $this->getConfiguration('listdisplay');
		$list = $this->getConfiguration('list');

		if (!is_array($item)) {
			// single value item field
			$items = array($item);
		} else {
			// item field has multiple values
			$items = $item;
		}

		$labels = array();

		foreach ($items as $key => $value) {
			if (!empty($dlist) && isset($dlist[$value])) {
				$labels[] = $dlist[$value];
			} else if (isset($list[$value])) {
				$labels[] = $list[$value];
			}
		}

		$label = implode(', ', $labels);

		if ($item && !is_array($item) && $context['list_mode'] !== 'csv' && $this->getOption('fieldId')) {
			$smarty->loadPlugin('smarty_function_object_link');

			if ( $this->getOption('linkPage') ) {
				$link = smarty_function_object_link(
					array(
						'type' => 'wiki page',
						'id' => $this->getOption('linkPage') . '&itemId=' . $item,	// add itemId param TODO properly
						'title' => $label,
					),
					$smarty
				);
				// decode & and = chars
				return str_replace(array('%26','%3D'), array('&','='), $link);
			} else {
				return smarty_function_object_link(array('type' => 'trackeritem',	'id' => $item,	'title' => $label), $smarty);
			}
		} elseif ($context['list_mode'] == 'csv' && $item) {
			return $item;
		} elseif ($label) {
			return $label;
		}
	}

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		$item = $this->getValue();
		$label = TikiLib::lib('object')->get_title('trackeritem', $item);

		$out = array(
			$baseKey => $typeFactory->identifier($item),
			"{$baseKey}_text" => $typeFactory->plaintext($label),
		);

		$indexRemote = array_filter(explode('|', $this->getOption('indexRemote')));
		if (count($indexRemote) && is_numeric($item)) {
			$utilities = new Services_Tracker_Utilities;
			$trackerId = $this->getOption('trackerId');
			$itemData = $utilities->getItem($trackerId, $item);

			$definition = Tracker_Definition::get($trackerId);
			foreach ($indexRemote as $fieldId) {
				$field = $definition->getField($fieldId);
				$permName = $field['permName'];

				$out["{$baseKey}_{$permName}"] = $typeFactory->plaintext($itemData['fields'][$permName]);
			}
		}

		return $out;
	}

	function getProvidedFields($baseKey)
	{
		$fields = array($baseKey, "{$baseKey}_text");

		$trackerId = $this->getOption('trackerId');
		$indexRemote = array_filter(explode('|', $this->getOption('indexRemote')));

		if (count($indexRemote)) {
			if ($definition = Tracker_Definition::get($trackerId)) {
				foreach ($indexRemote as $fieldId) {
					$field = $definition->getField($fieldId);
					$permName = $field['permName'];

					$fields[] = "{$baseKey}_{$permName}";
				}
			}
		}

		return $fields;
	}

	function getGlobalFields($baseKey)
	{
		return array();
	}

	/**
	 * Return both the current values and the list of available values
	 * for this field. When creating or updating a tracker item this
	 * function is called twice. First before the data is saved and then
	 * before displaying the changed tracker item to the user.
	 *
	 * @param array $requestData
	 * @param $string_id
	 * @return array
	 */
	private function getLinkData($requestData, $string_id)
	{
		$data = array(
			'value' => isset($requestData[$string_id]) ? $requestData[$string_id] : $this->getValue(),
		);

		if (!$this->getOption('displayFieldsList')) {
			$data['list'] = TikiLib::lib('trk')->get_all_items(
				$this->getOption('trackerId'),
				$this->getOption('fieldId'),
				$this->getOption('status', 'opc'),
				false
			);
			if (! $this->getOption('displayOneItem') || $this->getOption('displayOneItem') != 'multi') {
				// This silently modifies tracker items when an item name is used multiple times, which really isn't impossible.
				// If you want it, make it optional.
				//$data['list'] = array_unique($data['list']);
			} elseif (array_unique($data['list']) != $data['list']) {
				$newlist = array();
				foreach ($data['list'] as $k => $dl) {
					if (in_array($dl, $newlist)) {
						$dl = $dl . " ($k)";
					}
					$newlist[$k] = $dl;
				}
				$data['list'] = $newlist;
			}
		} else {
			$data['list'] = TikiLib::lib('trk')->get_all_items(
				$this->getOption('trackerId'),
				$this->getOption('fieldId'),
				$this->getOption('status', 'opc'),
				false
			);
			$data['listdisplay'] = array_unique(
				TikiLib::lib('trk')->concat_all_items_from_fieldslist(
					$this->getOption('trackerId'),
					$this->getOption('linkToItem'),
					$this->getOption('status', 'opc')
				)
			);
			if (!$this->getOption('displayOneItem') || $this->getOption('displayOneItem') != 'multi') {
				$data['list'] = array_unique($data['list']);
				$data['listdisplay'] = array_unique($data['listdisplay']);
			} elseif (array_unique($data['listdisplay']) != $data['listdisplay']) {
				$newlist = array();
				foreach ($data['listdisplay'] as $k => $dl) {
					if (in_array($dl, $newlist)) {
						$dl = $dl . " ($k)";
					}
					$newlist[$k] = $dl;
				}
				$data['listdisplay'] = $newlist;
			}
		}

		if ($this->getOption('addItems')) {
			$data['list']['-1'] = $this->getOption('addItems');
			if (isset($data['listdisplay'])) {
				$data['listdisplay']['-1'] = $this->getOption('addItems');
			}
		}

		// selectMultipleValues
		if ($this->getOption('selectMultipleValues') && !is_array($data['value'])) {
			$data['value'] = explode(',', $data['value']);
		}

		return $data;
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
		$sourceOptions = explode(',', $info['options']);
		$trackerId = isset($sourceOptions[0]) ? (int) $sourceOptions[0] : 0;
		$fieldId = isset($sourceOptions[1]) ? (int) $sourceOptions[1] : 0;
		$status = isset($sourceOptions[4]) ? (int) $sourceOptions[4] : 'opc';

		$info['type'] = 'd';
		$info['options'] = $this->getRemoteItemLinks($syncInfo, $trackerId, $fieldId, $status);

		return $info;
	}

	private function getRemoteItemLinks($syncInfo, $trackerId, $fieldId, $status)
	{
		$controller = new Services_RemoteController($syncInfo['provider'], 'tracker');
		$items = $controller->getResultLoader('list_items', array('trackerId' => $trackerId, 'status' => $status));
		$result = $controller->edit_field(array('trackerId' => $trackerId, 'fieldId' => $fieldId));

		$permName = $result['field']['permName'];
		if (empty($permName)) {
			return '';
		}

		$parts = array();
		foreach ($items as $item) {
			$parts[] = $item['itemId'] . '=' . $item['fields'][$permName];
		}

		return implode(',', $parts);
	}

	private function getPreselection()
	{
		$trklib = TikiLib::lib('trk');

		$localField = $this->getOption('preSelectFieldHere');
		$remoteField = $this->getOption('preSelectFieldThere');
		$method = $this->getOption('preSelectFieldMethod');
		$localTrackerId = $this->getConfiguration('trackerId');
		$remoteTrackerId = $this->getOption('trackerId');

		$localValue = $trklib->get_item_value($localTrackerId, $this->getItemId(), $localField);

		if ($method == 'domain') {
			if (! preg_match('@^(?:http://)?([^/]+)@i', $localValue, $matches)) {
				return '';
			}
			$host = $matches[1];
			preg_match('/[^.]+\.[^.]+$/', $host, $matches);
			$domain = $matches[0];
			if (strlen($domain) > 6) {
				// avoid com.sg or similar country subdomains
				$localValue = $domain;
			} else {
				$localValue = $host;
			}
		}

		if ($method == 'domain' || $method == 'partial') {
			$partial = true;
		} else {
			$partial = false;
		}

		return $trklib->get_item_id($remoteTrackerId, $remoteField, $localValue, $partial);
	}

	function handleSave($value, $oldValue)
	{
		// if selectMultipleValues is enabled, convert the array
		// of options to string before saving the field value in the db
		if ($this->getOption('selectMultipleValues')) {
			$value = implode(',', $value);
		}

		return array(
			'value' => $value,
		);
	}

	function itemsRequireRefresh($trackerId, $modifiedFields)
	{
		if ($this->getOption('trackerId') != $trackerId) {
			return false;
		}

		$usedFields = array_merge(
			array($this->getOption('fieldId')),
			explode('|', $this->getOption('indexRemote')),
			explode('|', $this->getOption('displayFieldsList'))
		);

		$intersect = array_intersect($usedFields, $modifiedFields);

		return count($intersect) > 0;
	}

	function cascadeCategories($trackerId)
	{
		return $this->cascade($trackerId, self::CASCADE_CATEG);
	}

	function cascadeStatus($trackerId)
	{
		return $this->cascade($trackerId, self::CASCADE_STATUS);
	}

	function cascadeDelete($trackerId)
	{
		return $this->cascade($trackerId, self::CASCADE_DELETE);
	}

	private function cascade($trackerId, $flag)
	{
		if ($this->getOption('trackerId') != $trackerId) {
			return false;
		}

		return ($this->getOption('cascade') & $flag) > 0;
	}

	function watchCompare($old, $new)
	{

		$data = $this->getLinkData(array(), 0);		// get old and new values directly from the list(s)
		$dlist = $data['listdisplay'];
		$list = $data['list'];

		if (!empty($dlist)) {
			$o = isset($dlist[$old]) ? $dlist[$old] : '';
			$n = isset($dlist[$new]) ? $dlist[$new] : '';
		} else {
			$o = isset($list[$old]) ? $list[$old] : '';
			$n = isset($list[$new]) ? $list[$new] : '';
		}

		return parent::watchCompare($o, $n);	// then compare as text
	}

}

