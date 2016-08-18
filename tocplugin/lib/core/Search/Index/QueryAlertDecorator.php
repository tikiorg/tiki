<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Index_QueryAlertDecorator extends Search_Index_AbstractIndexDecorator
{
	function addDocument(array $document)
	{
		$matches = $this->parent->getMatchingQueries($document);

		if (count($matches)) {
			$raw = TikiLib::lib('unifiedsearch')->getRawArray($document);
			foreach ($matches as $match) {
				list($priority, $id) = explode('-', $match, 2);
				TikiLib::events()->trigger('tiki.query.' . $priority, array(
					'query' => $id,
					'priority' => $priority,
					'user' => $GLOBALS['user'],
					'type' => $raw['object_type'],
					'object' => $raw['object_id'],
				));
			}
		}
		return $this->parent->addDocument($document);
	}
}

