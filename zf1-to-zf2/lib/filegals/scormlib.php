<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ScormLib
{
	private $unlinkList = array();

	function __destruct()
	{
		foreach ($this->unlinkList as $file) {
			unlink($file);
		}
	}

	function handle_file_creation($args)
	{
		if ($metadata = $this->getRequestMetadata($args)) {
			$this->createItem(
				$metadata,
				array(
					'scormPackage' => $args['object'],
				)
			);
		}
	}

	function handle_file_update($args)
	{
		if (isset($args['initialFileId']) && $metadata = $this->getRequestMetadata($args)) {
			$relationlib = TikiLib::lib('relation');
			$items = $relationlib->get_relations_to('file', $args['initialFileId'], 'tiki.file.attach');

			$transaction = TikiDb::get()->begin();

			foreach ($items as $item ) {
				if ($item['type'] == 'trackeritem') {
					$this->updateItem(
						$item['itemId'],
						$metadata,
						array(
							'scormPackage' => $args['object'],
						)
					);
				}
			}

			$transaction->commit();
		}
	}

	private function getRequestMetadata($args)
	{
		$metadata = null;

		if ($this->isZipFile($args)
			&& $zip = $this->getZipFile($args['object'])) {

			if ($manifest = $this->getScormManifest($zip)) {
				$metadata = $this->getMetadata($manifest);
			}

			$zip->close();
		}

		return $metadata;
	}

	private function isZipFile($args)
	{
		if (! isset($args['filetype'])) {
			return false;
		}

		return in_array($args['filetype'], array('application/zip', 'application/x-zip', 'application/x-zip-compressed'));
	}

	private function getZipFile($fileId)
	{
		global $prefs;

		if (! class_exists('ZipArchive')) {
			return null;
		}

		$filegallib = TikiLib::lib('filegal');

		if (! $info = $filegallib->get_file_info($fileId, false, true, false)) {
			return null;
		}

		$zip = new ZipArchive;

		if ($info['path'] && $prefs['fgal_use_db'] == 'n') {
			$filepath = $prefs['fgal_use_dir'] . $info['path'];
		} else {
			$filepath = tempnam('temp/', 'scorm');
			file_put_contents($filepath, $info['data']);
			$this->unlinkList[] = $filepath;
		}

		if ($zip->open($filepath) === TRUE) {
			return $zip;
		}
	}

	private function getScormManifest($zip)
	{
		return $zip->getFromName('imsmanifest.xml');
	}

	private function getMetadata($manifest)
	{
		$dom = new DOMDocument;
		$dom->loadXML($manifest);

		$metadata = array();
		foreach ($dom->getElementsByTagName('general')->item(0)->childNodes as $node) {
			if ($node instanceof DOMElement) {
				$metadata[$this->getKey($node)][] = $this->getData($node);
			}
		}

		return $metadata;
	}

	private function getKey($node)
	{
		$raw = $node->tagName;
		return substr($raw, strpos($raw, ':') + 1);
	}

	private function getData($node)
	{
		$data = array();
		foreach ($node->getElementsByTagName('langstring') as $text) {
			$data[$text->getAttribute('xml:lang')] = trim($text->textContent);
		}

		return $data;
	}

	private function createItem($metadata, $additional)
	{
		$definition = $this->getScormTracker();
		$fields = $this->buildFields($definition, $metadata, $additional);

		$utilities = new Services_Tracker_Utilities;
		$utilities->insertItem(
			$definition,
			array(
				'status' => 'o',
				'fields' => $fields,
			)
		);
	}

	private function updateItem($itemId, $metadata, $additional)
	{
		$definition = $this->getScormTracker();
		$fields = $this->buildFields($definition, $metadata, $additional);

		$utilities = new Services_Tracker_Utilities;
		$utilities->updateItem(
			$definition,
			array(
				'itemId' => (int) $itemId,
				'status' => 'o',
				'fields' => $fields,
			)
		);
	}

	private function getScormTracker()
	{
		global $prefs;

		return Tracker_Definition::get($prefs['scorm_tracker']);
	}

	private function buildFields($definition, $metadata, $additional)
	{
		$fields = array();

		foreach ($metadata as $key => $values) {
			if ($field = $definition->getFieldFromPermName($key)) {
				if ($field['type'] === 'F') {
					$fields[$key] = $this->getTagString($values);
				} elseif ($field['isMultilingual'] == 'y') {
					$fields[$key] = reset($values);
				} else {
					$fields[$key] = $this->getForDefaultLanguage($values);
				}
			}
		}

		return array_merge($fields, $additional);
	}

	private function getTagString($values)
	{
		$completeSet = array();
		foreach ($values as $val) {
			$completeSet = array_merge($completeSet, array_values($val));
		}

		return implode(' ', array_unique($completeSet));
	}

	private function getForDefaultLanguage($values)
	{
		global $prefs;

		$defaultLanguage = $prefs['language'];
		foreach ($values as $valueSet) {
			if (isset($valueSet[$defaultLanguage])) {
				return $valueSet[$defaultLanguage];
			}
		}

		// If nothing matching the language found, return the first value
		return reset(reset($values));
	}
}

