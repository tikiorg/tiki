<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Writer
{
	private $data;
	private $filePath;
	private $dataPath;
	private $references = array();

	function __construct($directory, $profileName)
	{
		$this->filePath = "$directory/$profileName.yml";
		$this->dataPath = "$directory/$profileName";
		if (file_exists($this->filePath)) {
			$content = file_get_contents($this->filePath);
			$this->data = Horde_Yaml::load($content);
		} else {
			$this->data = array(
				'preferences' => array(),
				'objects' => array(),
			);
		}
	}

	function pushReference($name)
	{
		$this->references[] = $name;
	}

	function writeExternal($page, $content)
	{
		file_put_contents("{$this->dataPath}/$page.wiki", $content);
	}

	function addObject($type, $currentId, array $data)
	{
		$this->clearObject($type, $currentId);

		$reference = array_shift($this->references);

		if (! $reference) {
			$reference = $type . '_' . preg_replace('/[^\w]+/', '', strtolower($currentId));
		}

		$this->data['objects'][] = array(
			'type' => $type,
			'ref' => $reference,
			'_id' => $currentId,
			'data' => $data,
		);

		return $reference;
	}

	function getReference($type, $id)
	{
		if (is_array($id)) {
			$parent = $this;
			return array_map(function ($value) use ($type, $parent) {
				return $parent->getReference($type, $value);
			}, $id);
		}

		foreach ($this->data['objects'] as $object) {
			if ($object['type'] == $type && $object['_id'] == $id) {
				return "\$profileobject:{$object['ref']}\$";
			}
		}

		return $id;
	}

	private function clearObject($type, $id)
	{
		$this->data['objects'] = array_filter($this->data['objects'], function ($item) use ($type, $id) {
			return $item['type'] != $type || $item['_id'] != $id;
		});
	}

	function save()
	{
		file_put_contents($this->filePath, Horde_Yaml::dump($this->data));
	}

	function clean()
	{
		array_walk($this->data['objects'], function (& $entry) {
			unset($entry['_id']);
		});
	}

	function dump()
	{
		$clone = clone $this;
		$clone->clean();

		return Horde_Yaml::dump($clone->data);
	}
}
