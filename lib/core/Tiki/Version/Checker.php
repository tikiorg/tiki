<?php

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

		$content = call_user_func($callback, "http://tiki.org/{$this->cycle}.cycle");
		$versions = $this->getSupportedVersions($content);

		$currentIsSupported = false;

		foreach ($versions as $supported) {
			if ($supported->getMajor() == $this->version->getMajor()) {
				$currentIsSupported = true;

				if ($supported->isUpgradeTo($this->version)) {
					$upgrades[] = new Tiki_Version_Upgrade($this->version, $supported, true);
				}
			}
		}

		$max = $this->getLatestVersion($versions);

		if ($max->isUpgradeTo($this->version)) {
			$upgrades[] = new Tiki_Version_Upgrade($this->version, $max, ! $currentIsSupported);
		}

		return $upgrades;
	}

	private function getSupportedVersions($content)
	{
		return array_filter(array_map(array('Tiki_Version_Version', 'get'), explode("\n", $content)));
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

