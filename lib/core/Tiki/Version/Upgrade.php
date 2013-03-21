<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Version_Upgrade
{
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
				$parts[] = tr('A minor upgrade to %0 is required.', (string) $this->new);
			} else {
				$parts[] = tr('A major upgrade to %0 is required.', (string) $this->new);
			}
		} else {
			$parts[] = tr('Version %0 is still supported. However, a major upgrade to %1 is available.', (string) $this->old, (string) $this->new);
		}

		return implode(' ', $parts);
	}

	private function isMinor()
	{
		return $this->old->getMajor() === $this->new->getMajor();
	}
}

