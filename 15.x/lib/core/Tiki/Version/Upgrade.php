<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Version_Upgrade
{
	// old actually means current
	private $old;
	private $new;
	private $isRequired;

	function __construct($old, $new, $isRequired)
	{
		$this->old = Tiki_Version_Version::get($old);
		$this->new = Tiki_Version_Version::get($new);
		$this->isRequired = $isRequired;
	}

	function getMessage()
	{
		$parts = array();
		if ($this->isRequired) {
			$parts[] = tr('Version %0 is no longer supported.', (string) $this->old);

			if ($this->isMinor()) {
				$parts[] = tr('A minor upgrade to %0 is strongly recommended.', (string) $this->new);
			} else {
				$parts[] = tr('A major upgrade to %0 is strongly recommended.', (string) $this->new);
			}
		} else {
			// Do not encourage people to leave an LTS which is still supported. Just inform them
			$current = $this->old;
			$current_major = (strstr($current,'.',true) != false)?strstr($current,'.',true):$current;
			if (in_array($current_major,array('9','12','15'))) {	// Keep list of LTS up to date or write method isLTS, whichever is less work
				$current = "$current LTS";
				$parts[] = tr('Version %0 is still supported. However, an upgrade to %1 is available.', $current, (string) $this->new);
			} else {
				$parts[] = tr('Version %0 is still supported. However, a major upgrade to %1 is available.', (string) $this->old, (string) $this->new);
			}
		}

		return implode(' ', $parts);
	}

	private function isMinor()
	{
		return $this->old->getMajor() === $this->new->getMajor();
	}
}

