<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for GroupSelector
 * 
 * Letter key: ~g~
 *
 *	Options:
 *		0: auto-assign =
 *			0 = general
 *			1 = creator
 *			2 = modifier
 *
 */
class Tracker_Field_GroupSelector extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		global $tiki_p_admin_trackers, $group;
		
		$ins_id = $this->getInsertId();

		$data = array();
		
		if ( isset($requestData[$ins_id])) {
			if ($this->getOption(0) < 1 || $tiki_p_admin_trackers === 'y') {
				$data['value'] = $requestData[$ins_id];
			} else {
				if ($this->getOption(0) == 2) {
					$data['defvalue'] = $group;
					$data['value'] = $group;
				} elseif ($this->getOption(0) == 1) {
					$data['value'] = $group;
				} else {
					$data['value'] = '';
				}
			}
		} else {
			$data['defvalue'] = $group;
			$data['value'] = $this->getValue();		
		}
		
		$data['list'] = TikiLib::lib('user')->list_all_groups();
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/groupselector.tpl', $context);
	}
}

