<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Auto increment
 * 
 * Letter key: ~q~
 *
 */
class Tracker_Field_AutoIncrement extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$value = isset($requestData[$ins_id]) ? $requestData[$ins_id] : $this->getValue();

		$append = $this->getOption(1);
		if (!empty($append)) {
			$value = "<span class='formunit'>$append</span>" . $value;
		}
	
		$prepend = $this->getOption(2);
		if (!empty($prepend)) {
			$value .= "<span class='formunit'>$prepend</span>";
		}
			
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/autoincrement.tpl', $context);
	}
}

