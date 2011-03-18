<?php

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
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/groupselector.tpl', $context);
	}
}

