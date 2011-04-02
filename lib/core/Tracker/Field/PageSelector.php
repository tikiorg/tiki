<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for PageSelector
 * 
 * Letter key: ~k~
 * Possibly doesn't need "non-simple" handling apart from defaultvalue?
 *
 */
class Tracker_Field_PageSelector extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => isset($requestData[$ins_id])
				? $requestData[$ins_id]
				: $this->getValue(),
			'defaultvalue' => $this->getOption(2)
				? $this->getOption(2)
				: $this->getValue(),
		);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/pageselector.tpl', $context);
	}
	
	function renderOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		if ($this->getOption(3) === 'n' || (isset($context['list_mode']) && $context['list_mode'] === 'csv')) {
			return $value;
		} else {
			$smarty = TikiLib::lib('smarty');
			require_once $smarty->_get_plugin_filepath('function', 'object_link');
			return smarty_function_object_link( array(
				'type' => 'wikipage',
				'id' => $value,
			), $smarty);
		}
	}
}

