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
class Tracker_Field_ItemLink extends Tracker_Field_Abstract
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
				),
			),
		);
	}

	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		return new self($fieldInfo, $itemData, $trackerDefinition);
	}

	function getFieldData(array $requestData = array())
	{
		$data = $this->getLinkData($requestData, $this->getInsertId());

		$value = $data['value'];

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/itemlink.tpl', $context);
	}

	function renderOutput($context = array())
	{
		$smarty = TikiLib::lib('smarty');

		$item = $this->getConfiguration('value');
		$dlist = $this->getConfiguration('listdisplay');
		$list = $this->getConfiguration('list');
		if (!empty($dlist)) {
			$label = $dlist[$item];
		} else {
			$label = $list[$item];
		}
		if ($item && $context['list_mode'] !== 'csv' && $this->getOption(2)) {
			require_once $smarty->_get_plugin_filepath('function', 'object_link');

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
					$this->getOption(4, 'poc'),
					false
				)
			);
		} else {
			$data['list'] = TikiLib::lib('trk')->get_all_items(
				$this->getOption(0),
				$this->getOption(1),
				$this->getOption(4, 'poc'),
				false
			);
			$data['listdisplay'] = array_unique(
				TikiLib::lib('trk')->concat_all_items_from_fieldslist(
					$this->getOption(0),
					$this->getOption(3),
					$this->getOption(4, 'poc')
				)
			);
		}

		return $data;
	}
}

