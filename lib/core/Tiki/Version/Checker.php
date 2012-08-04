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
		$content = call_user_func($callback, "http://tiki.org/{$this->cycle}.cycle");
		$versions = $this->getSupportedVersions($content);

		foreach ($versions as $supported) {
			if ($supported->getMajor() == $this->version->getMajor()) {
				if ($supported->isUpgradeTo($this->version)) {
					return new Tiki_Version_Upgrade($this->version, $supported);
				} else {
					return null;
				}
			}
		}

		$max = array_shift($versions);

		foreach ($versions as $candidate) {
			if ($candidate->isUpgradeTo($max)) {
				$max = $candidate;
			}
		}

		if ($max->isUpgradeTo($this->version)) {
			return new Tiki_Version_Upgrade($this->version, $max);
		}
	}

	function getSupportedVersions($content)
	{
		return array_filter(array_map(array('Tiki_Version_Version', 'get'), explode("\n", $content)));
	}
}

