<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
	public static function getTypes()
	{
		return array(
			'r' => array(
				'name' => tr('Item Link'),
				'description' => tr('Link to an other item, similar to a foreign key.'),
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
						'description' => tr('Display the values from multiple fields instead of a single one.'),
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
		if ($this->getOption(6) && $context['inTable'] !== 'y') {
			require_once 'lib/wiki-plugins/wikiplugin_tracker.php';

			$form = wikiplugin_tracker('', array(
					'trackerId' => $this->getOption(0),
					'ignoreRequestItemId' => 'y',
					'_ajax_form_ins_id' => $this->getInsertId(),
				));

			$form = preg_replace(array('/<!--.*?-->/', '/\s+/'), array('', ' '), $form);	// remove comments etc

			TikiLib::lib('header')->add_jq_onready('
$("select[name=' . $this->getInsertId() . ']").change(function(e, val) {
	if ($(this).val() == -1) {
		var $d = $("<div id=\'add_dialog_' . $this->getInsertId() . '\' style=\'display:none\'>' . addslashes($form) . '</div>")
			.appendTo(document.body);
		
		var w = $d.width() * 1.4;
		var h = $d.height() * 1.6;
		if ($(document.body).width() < w) {
			w = $(document.body).width() * 0.8;
		}
		if ($(document.body).height() < h) {
			h = $(document.body).height() * 0.8;
		}

		$d.dialog({
				width: w,
				height: h,
				title: "'.$this->getOption(6).'",
				modal: true,
				buttons: {
					"Add": function() {
						var $f = $("form", this).append($("<input type=\'hidden\' name=\'ajax_add\' value=\'1\' />"));
						ajaxLoadingShow($f);
						$.post( $f.attr("action"), $f.serialize(), function(data, status) {
							var m = data.match(/.*(\{"data":.*)/);	// strip the beginning of the page as smarty sends stuff before this can be sent FIXME
							if (m && m.length > 0) {
								m = $.secureEvalJSON(m[1]);
								for (var i = 0; i < m.data.length; i++) {
									var a = m.data[i];
									if ( a && a["fieldId"] == '.$this->getOption(1).' ) {
										$o = $("<option value=\'" + a["fileId"] + "\'>" + a["value"] + "</option>");
										$("select[name=' . $this->getInsertId() . '] > option:first").after($o);
										$("select[name=' . $this->getInsertId() . ']")[0].selectedIndex = 1;
									}
								}
							}
							ajaxLoadingHide();
							$d.dialog( "close" );

							return;
						});


							//.append($("<input type=\'hidden\' name=\'save\' value=\'save\' />"))
							//.submit();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				},
				create: function(event, ui) {
					 ajaxTrackerFormInit_' . $this->getInsertId() . '();
				}
			}).find(".input_submit_container").remove();
	}
});
');

		}

		return $this->renderTemplate('trackerinput/itemlink.tpl', $context);
	}

	function renderOutput($context = array())
	{
		$smarty = TikiLib::lib('smarty');

		$item = $this->getConfiguration('value');
		$dlist = $this->getConfiguration('listdisplay');
		$list = $this->getConfiguration('list');
		if (!empty($dlist)) {
			$label = isset($dlist[$item]) ? $dlist[$item] : '';
		} else {
			$label = isset($list[$item]) ? $list[$item] : '';
		}
		if ($item && $context['list_mode'] !== 'csv' && $this->getOption(2)) {
			$smarty->loadPlugin('smarty_function_object_link');

			return smarty_function_object_link(array(
				'type' => 'trackeritem',
				'id' => $item,
				'title' => $label,
			), $smarty);
		} elseif ($label) {
			return $label;
		}
	}

	private function getLinkData($requestData, $string_id)
	{
		$data = array(
			'value' => isset($requestData[$string_id]) ? $requestData[$string_id] : $this->getValue(),
		);

		if (!$this->getOption(3)) {	//no displayedFieldsList
			$data['list'] = array_unique(
				TikiLib::lib('trk')->get_all_items(
					$this->getOption(0),
					$this->getOption(1),
					$this->getOption(4, 'opc'),
					false
				)
			);
		} else {
			$data['list'] = TikiLib::lib('trk')->get_all_items(
				$this->getOption(0),
				$this->getOption(1),
				$this->getOption(4, 'opc'),
				false
			);
			$data['listdisplay'] = array_unique(
				TikiLib::lib('trk')->concat_all_items_from_fieldslist(
					$this->getOption(0),
					$this->getOption(3),
					$this->getOption(4, 'opc')
				)
			);
		}

		if ($this->getOption(6)) {	// addItems
			$data['list']['-1'] = $this->getOption(6);
			if (isset($data['listdisplay'])) {
				$data['listdisplay']['-1'] = $this->getOption(6);
			}
		}
		
		return $data;
	}

	function import($value)
	{
		return $value;
	}

	function export($value)
	{
		return $value;
	}

	function importField(array $info, array $syncInfo)
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
		$items = $controller->getResultLoader('list_items', array(
			'trackerId' => $trackerId,
			'status' => $status,
		));
		$result = $controller->edit_field(array(
			'trackerId' => $trackerId,
			'fieldId' => $fieldId,
		));

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
}

