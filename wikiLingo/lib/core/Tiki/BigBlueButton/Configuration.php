<?php

namespace Tiki\BigBlueButton;

class Configuration
{
	private $dom;

	function __construct($xmlString)
	{
		if ($xmlString instanceof \DOMDocument) {
			$this->dom = $xmlString;
		} else {
			$this->dom = new \DOMDocument;
			$this->dom->loadXML($xmlString);;
		}
	}
	
	function getXml()
	{
		return $this->dom->saveXML();
	}

	function removeModule($moduleName)
	{
		$toRemove = $this->getRemoveList($moduleName);

		foreach ($toRemove as $node) {
			$node->parentNode->removeChild($node);
		}
	}

	private function getRemoveList($moduleName)
	{
		$toRemove = array();
		$modules = $this->dom->getElementsByTagName('module');
		foreach ($modules as $node) {
			if ($node->getAttribute('name') == $moduleName) {
				$toRemove[] = $node;
			}

			if ($node->getAttribute('dependsOn') == $moduleName) {
				$toRemove = array_merge($toRemove, $this->getRemoveList($node->getAttribute('name')));
			}
		}

		return $toRemove;
	}
}

