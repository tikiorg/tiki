<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Static text
 * 
 * Letter key: ~S~
 *
 */
class Tracker_Field_StaticText extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		global $tikilib;
		
		$value = $this->getConfiguration('description');

		if ($this->getOption(0) == 1) {
			$value = $tikilib->parse_data($value);
		}
		
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/statictext.tpl', $context);
	}
}

