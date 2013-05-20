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
	private $externalWriter;
	private $references = array();

	function __construct($directory, $profileName)
	{
		$this->filePath = "$directory/$profileName.yml";
		$this->externalWriter = new Tiki_Profile_ExternalWriter("$directory/$profileName");
		if (file_exists($this->filePath)) {
			$content = file_get_contents($this->filePath);
			$this->data = Horde_Yaml::load($content);
		} else {
			$this->data = array(
				'preferences' => array(),
				'objects' => array(),
				'unknown_objects' => array(),
			);
		}
	}

	function pushReference($name)
	{
		$this->references[] = $name;
	}

	function writeExternal($page, $content)
	{
		$this->externalWriter->write("$page.wiki", $content);
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

		// Search through currently unknown reference for this item
		$ref = $this->getReference($type, $currentId);
		$this->removeUnknown($type, $currentId, $ref);

		return $reference;
	}

	function removeUnknown($type, $id, $replacement)
	{
		foreach ($this->data['unknown_objects'] as $key => $entry) {
			if ($entry['type'] == $type && $entry['id'] == $id) {
				$token = $entry['token'];
				array_walk_recursive($this->data['objects'], function (& $entry) use ($token, $replacement) {
					if (is_string($entry)) {
						$entry = str_replace($token, $replacement, $entry);
					}
				});

				$writer = $this->externalWriter;
				foreach ($writer->getFiles() as $file => $content) {
					$content = str_replace($token, $replacement, $content);
					$writer->write($file, $content);
				}

				unset($this->data['unknown_objects'][$key]);
				break;
			}
		}
	}

	function getUnknownObjects()
	{
		return array_map(function ($entry) {
			unset($entry['token']);
			return $entry;
		}, $this->data['unknown_objects']);
	}

	/**
	 * Obtain the replacement string for the given type and id. Type can also be a callback
	 * or a helper name for complex transformations that require parsing and are called through
	 * the declarative interfaces (plugins, field types, preferences, ...). Extra parameters are
	 * provided in those cases since the type may depend on other arguments.
	 */
	function getReference($type, $id, array $parameters = array())
	{
		// If we are provided with an anonymous function to handle special cases
		if (is_callable($type)) {
			return call_user_func($type, $this, $id, $parameters);
		} elseif (method_exists('Tiki_Profile_WriterHelper', $type)) {
			return Tiki_Profile_WriterHelper::$type($this, $id, $parameters);
		}

		// Let 'wiki page' or 'tracker item' be provided as type, no effect when profile types used
		$type = Tiki_Profile_Installer::convertTypeInvert($type);

		if (is_array($id)) {
			$parent = $this;
			return array_map(function ($value) use ($type, $parent) {
				return $parent->getReference($type, $value, $parameters);
			}, $id);
		}

		foreach ($this->data['objects'] as $object) {
			if ($object['type'] == $type && $object['_id'] == $id) {
				return "\$profileobject:{$object['ref']}\$";
			}
		}

		return $this->generateTemporaryReference($type, $id);
	}

	function formatExternalReference($symbol, $profile, $repository = null)
	{
		if ($repository) {
			$repository .= ':';
		}

		return "\$profileobject:$repository$profile:$symbol\$";
	}

	private function clearObject($type, $id)
	{
		$this->data['objects'] = array_filter($this->data['objects'], function ($item) use ($type, $id) {
			return $item['type'] != $type || $item['_id'] != $id;
		});
	}

	private function generateTemporaryReference($type, $id)
	{
		// Find existing entry for unknown reference
		foreach ($this->data['unknown_objects'] as $entry) {
			if ($entry['type'] == $type && $entry['id'] == $id) {
				return $entry['token'];
			}
		}

		// Or generate a new one
		$token = '$unknownobject:' . uniqid() . '$';
		$this->data['unknown_objects'][] = array(
			'type' => $type,
			'id' => $id,
			'token' => $token,
		);

		return $token;
	}

	function save()
	{
		file_put_contents($this->filePath, Horde_Yaml::dump($this->data));
		$this->externalWriter->apply();
	}

	function clean()
	{
		array_walk($this->data['objects'], function (& $entry) {
			unset($entry['_id']);
		});
		unset($this->data['unknown_objects']);
	}

	function dump()
	{
		$clone = clone $this;
		$clone->clean();

		return Horde_Yaml::dump($clone->data);
	}
}
