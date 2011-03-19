<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for LDAP. Was not extensively tested after migration.
 * 
 * Letter key: ~P~
 *
 */
class Tracker_Field_Ldap extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		if ($this->getOption(2)) {
			$adminlib = TikiLib::lib('admin');
			$ldaplib = TikiLib::lib('ldap');

			// Retrieve DSN
			$info_ldap = $adminlib->get_dsn_from_name($this->getOption(2));

			if ($info_ldap) {
				$ldap_filter = $this->getOption(0);

				// Replace %field_name% by real value
				preg_match('/%([^%]+)%/', $ldap_filter, $ldap_filter_field_names);

				if (isset($ldap_filter_field_names[1])) {
					$field = $this->getTrackerDefinition()->getFieldFromName($ldap_filter_field_names[1]);

					if ($field) {
						$value = TikiLib::lib('trk')->get_field_value($field, $this->getItemData());

						$ldap_filter = preg_replace('/%'. $ldap_filter_field_names[1] .'%/', $value, $ldap_filter);

						// Get LDAP field value
						return array(
							'value' => $ldaplib->get_field($info_ldap['dsn'], $ldap_filter, $this->getOption(1)),
						);
					}
				}
			}
		}
	}

	function renderInput($context = array())
	{
		return $this->getValue();
	}
}

