<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_FileAttachmentSource implements Search_GlobalSource_Interface
{
	private $relationlib;
	private $attributelib;
	private $fileSource;

	function __construct(Search_ContentSource_Interface $source)
	{
		$this->relationlib = TikiLib::lib('relation');
		$this->attributelib = TikiLib::lib('attribute');
		$this->fileSource = $source;
	}

	function getProvidedFields()
	{
		return ['attachment_contents', 'attachments', 'primary_image'];
	}

	function getGlobalFields()
	{
		return [
			'attachment_contents' => false,
		];
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = [])
	{
		$relations = $this->relationlib->get_relations_from($objectType, $objectId, 'tiki.file.attach');
		$attributes = $this->attributelib->get_attributes($objectType, $objectId);

		$textual = [];
		$files = [];

		foreach ($relations as $rel) {
			if ($rel['type'] == 'file') {
				$files[] = $rel['itemId'];
				if ($data = $this->fileSource->getDocument($rel['itemId'], $typeFactory)) {
					foreach ($this->fileSource->getGlobalFields() as $name => $keep) {
						$textual[] = $data[$name]->getValue();
					}
				} else {
					error_log("File " . $rel['itemId'] . ", referenced from " . $objectType . $objectId . " no longer exists.");
				}
			}
		}

		return [
			'attachments' => $typeFactory->multivalue($files),
			'attachment_contents' => $typeFactory->plaintext(implode(' ', $textual)),
			'primary_image' => $typeFactory->identifier(isset($attributes['tiki.object.image']) ? $attributes['tiki.object.image'] : ''),
		];
	}
}
