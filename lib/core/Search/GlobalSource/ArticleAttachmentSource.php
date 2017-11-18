<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_ArticleAttachmentSource implements Search_GlobalSource_Interface
{
	private $relationlib;
	private $source;

	function __construct(Search_ContentSource_Interface $source)
	{
		$this->relationlib = TikiLib::lib('relation');
		$this->source = $source;
	}

	function getProvidedFields()
	{
		return ['article_contents'];
	}

	function getGlobalFields()
	{
		return [
			'article_contents' => false,
		];
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = [])
	{
		$relations = $this->relationlib->get_relations_from($objectType, $objectId, 'tiki.article.attach');

		$textual = [];

		foreach ($relations as $rel) {
			if ($rel['type'] == 'article') {
				if ($data = $this->source->getDocument($rel['itemId'], $typeFactory)) {
					foreach ($this->source->getGlobalFields() as $name => $keep) {
						$textual[] = $data[$name]->getValue();
					}
				} else {
					error_log("File " . $rel['itemId'] . ", referenced from " . $objectType . $objectId . " no longer exists.");
				}
			}
		}

		return [
			'article_contents' => $typeFactory->plaintext(implode(' ', $textual)),
		];
	}
}
