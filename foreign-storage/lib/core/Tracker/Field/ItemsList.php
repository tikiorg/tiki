<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for ItemsList
 * 
 * Letter key: ~l~
 *
 */
class Tracker_Field_ItemsList extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array(
			'value' => $this->getValue(),
		);

		$trackerId = isset($requestData['trackerId'])
				? $requestData['trackerId']
				: $this->getConfiguration('trackerId');

		$itemId = isset($requestData['itemId'])
				? $requestData['itemId']
				: $this->getItemId();

		if ( $trackerId && $itemId ) {
			if ($this->getOption(3)) {
				$l = explode(':', $this->getOption(1));
				$finalFields = explode('|', $this->getOption(3));
				$data['links'] = TikiLib::lib('trk')->get_join_values(
						$trackerId, $itemId,
						array_merge( array($this->getOption(2)), $l, array($this->getOption(3))),
						$this->getOption(0), $finalFields,  ' ', $this->getOption(5)
				);
				if (count($data['links']) == 1) {
					foreach($data['links'] as $linkItemId => $linkValue) {
						if (is_numeric($data['links'][$linkItemId])) { // if later a computed field use this field
							$info[$this->getConfiguration('fieldId')] = $linkValue;	// TODO $info not defined in this scope?
						}
					}
				}
				$data['trackerId'] = $this->getOption(0);
				$data['tracker_options'] = TikiLib::lib('trk')->get_tracker_options($this->getOption(0));
			}
		}
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/itemslist.tpl', $context);
	}

	function renderOutput( $context = array() ) {
		if ($context['list_mode'] === 'csv') {
			return $this->getConfiguration('value');	// FIXME
		} else {
			return $this->renderInput( $context );
		}
	}
}

