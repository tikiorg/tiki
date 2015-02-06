<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Version_Checker
{
	private $cycle;
	private $version;

	function setCycle($cycle)
	{
		$this->cycle = $cycle;
	}

	function setVersion($version)
	{
		$this->version = Tiki_Version_Version::get($version);
	}

	function check($callback)
	{
		$upgrades = array();
		$branchupdate = null;

		$content = call_user_func($callback, "http://tiki.org/{$this->cycle}.cycle");
		$versions = $this->getSupportedVersions($content);

		if ($supported = $this->findSupportedInBranch($versions)) {
			if ($supported->isUpgradeTo($this->version)) {
				$upgrades[] = new Tiki_Version_Upgrade($this->version, $supported, true);
				$branchupdate = $supported;
			}
		}

		$max = $this->getLatestVersion($versions);

		if ($max !== $branchupdate && $max->isUpgradeTo($this->version)) {
			$upgrades[] = new Tiki_Version_Upgrade($supported ?: $this->version, $max, $supported === false);
		}

		return $upgrades;
	}

	private function getSupportedVersions($content)
	{
		return array_filter(array_map(array('Tiki_Version_Version', 'get'), explode("\n", $content)));
	}

	private function findSupportedInBranch($versions)
	{
		foreach ($versions as $supported) {
			if ($supported->getMajor() == $this->version->getMajor()) {
				return $supported;
			}
		}

		return false;
	}

	private function getLatestVersion($versions)
	{
		$max = array_shift($versions);

		foreach ($versions as $candidate) {
			if ($candidate->isUpgradeTo($max)) {
				$max = $candidate;
			}
		}

		return $max;
	}
}

