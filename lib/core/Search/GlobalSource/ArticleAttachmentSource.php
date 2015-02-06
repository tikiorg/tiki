<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		return array('article_contents');
	}

	function getGlobalFields()
	{
		return array(
			'article_contents' => false,
		);
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$relations = $this->relationlib->get_relations_from($objectType, $objectId, 'tiki.article.attach');

		$textual = array();

		foreach ($relations as $rel) {
			if ($rel['type'] == 'article') {
				$data = $this->source->getDocument($rel['itemId'], $typeFactory);

				foreach ($this->source->getGlobalFields() as $name => $keep) {
					$textual[] = $data[$name]->getValue();
				}
			}
		}

		return array(
			'article_contents' => $typeFactory->plaintext(implode(' ', $textual)),
		);
	}
}

