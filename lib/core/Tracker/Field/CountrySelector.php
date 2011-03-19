<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for CountrySelector
 * 
 * Letter key: ~y~
 *
 */
class Tracker_Field_CountrySelector extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array(
			'value' => isset($requestData[$ins_id])
				? $requestData[$ins_id]
				: $this->getValue(),
			'flags' => TikiLib::lib('trk')->get_flags(true, true, ($this->getOption(1) != 1)),
			'defaultvalue' => 'None',
		);
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/countryselector.tpl', $context);
	}
}

