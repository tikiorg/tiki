<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for UserSelector
 * 
 * Letter key: ~u~
 *
 *	Options:
 *		0: auto-assign =
 *			0 = general
 *			1 = creator
 *			2 = modifier
 *
 *		1: email_notify
 *			0/1
 */
class Tracker_Field_UserSelector extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		global $tiki_p_admin_trackers, $user;
		
		$ins_id = $this->getInsertId();

		$data = array();
		
		if ( isset($requestData[$ins_id])) {
			if ($this->getOption(0) < 1 || $tiki_p_admin_trackers === 'y') {
				$data['value'] = $requestData[$ins_id];
			} else {
				if ($this->getOption(0) == 2) {
					$data['value'] = $user;
				} elseif ($this->getOption(0) == 1) {
					if ($this->getTrackerDefinition()->getConfiguration('userCanTakeOwnership')  == 'y' && !$this->getValue()) {
						$data['value'] = $user; // the user appropiate the item
					} else {
						$data['value'] = $this->getValue();
						// unset($data['fieldId']); hmm?
					}
				} else {
					$data['value'] = '';
				}
			}
		} else {
			$data['value'] = $this->getValue();
		}
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		global $tiki_p_admin_trackers, $user;
		$smarty = TikiLib::lib('smarty');
		
		$value = $this->getValue() ? $this->getValue() : $user;
		
		if ($this->getOption(0) == 0 || $tiki_p_admin_trackers === 'y') {
			require_once $smarty->_get_plugin_filepath('function', 'user_selector');
			return smarty_function_user_selector(
					array(	'user' => $value,
							'id'  => 'user_selector_' . $this->getConfiguration('fieldId'),
							'name' => $this->getInsertId(),
							'editable' => 'y',
					), $smarty);
		} else {
			require_once $smarty->_get_plugin_filepath('modifier', 'username');
			return smarty_modifier_username( $value ) . '<input type="hidden" name="' . $this->getInsertId() . '">';
		}
	}

	function renderInnerOutput($context = array())
	{
		$value = $this->getValue();
		require_once TikiLib::lib('smarty')->_get_plugin_filepath('modifier', 'username');
		return smarty_modifier_username( $value );
	}

}

