<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_FileAttachmentSource implements Search_GlobalSource_Interface
{
	private $relationlib;
	private $fileSource;

	function __construct()
	{
		$this->relationlib = TikiLib::lib('relation');
		$this->fileSource = new Search_ContentSource_FileSource;
	}

	function getProvidedFields()
	{
		return array('attachment_contents', 'attachments');
	}

	function getGlobalFields()
	{
		return array(
			'attachment_contents' => false,
		);
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$relations = $this->relationlib->get_relations_from($objectType, $objectId, 'tiki.file.attach');

		$textual = array();
		$files = array();

		foreach ($relations as $rel) {
			if ($rel['type'] == 'file') {
				$files[] = $rel['itemId'];
				$data = $this->fileSource->getDocument($rel['itemId'], $typeFactory);

				foreach ($this->fileSource->getGlobalFields() as $name => $keep) {
					$textual[] = $data[$name]->getValue();
				}
			}
		}

		return array(
			'attachments' => $typeFactory->multivalue($files),
			'attachment_contents' => $typeFactory->plaintext(implode(' ', $textual)),
		);
	}
}

