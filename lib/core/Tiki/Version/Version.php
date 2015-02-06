<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Version_Version
{
	private $major;
	private $minor;
	private $extra;
	private $sub;
	private $number;

	function __construct($major, $minor, $extra = 0, $sub = 0, $number = 0)
	{
		$this->major = (int) $major;
		$this->minor = (int) $minor;
		$this->extra = $extra;
		$this->sub = $sub;
		$this->number = (int) $number;
	}

	public static function get($version)
	{
		if ($version instanceof self) {
			return $version;
		} else {
			preg_match('/^(\d+)\.(\d+)?(\.([\d\.]+))?((alpha|beta|rc|pre|svn)(\d*))?$/', $version, $parts);
			for ($i = 0; 8 > $i; ++$i) {
				if (! isset($parts[$i])) {
					$parts[$i] = null;
				}
			}

			return new self($parts[1], $parts[2], $parts[4], $parts[6], $parts[7]);
		}
	}

	function getMajor()
	{
		return $this->major;
	}

	function isUpgradeTo($version)
	{
		// Note that this does not cover all cases, upgrades are only official releases
		// and the 'extra' portion is ignored as only used by legacy versions, which anything
		// is an upgrade to

		if ($this->major > $version->major) {
			return true;
		} elseif ($this->major == $version->major && $this->minor > $version->minor) {
			return true;
		} elseif ($this->major == $version->major && $this->minor == $version->minor) {
			return empty($this->sub) && ! empty($version->sub);
		} else {
			return false;
		}
	}

	function __toString()
	{
		$string = "{$this->major}.{$this->minor}";

		if ($this->extra) {
			$string .= ".{$this->extra}";
		}

		if ($this->sub) {
			$string .= $this->sub;

			if ($this->number) {
				$string .= $this->number;
			}
		}

		return $string;
	}
}

