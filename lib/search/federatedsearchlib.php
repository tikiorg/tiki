<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class FederatedSearchLib
{
	private $unified;

	function __construct($unifiedsearch)
	{
		$this->unified = $unifiedsearch;
	}

	function augmentSimpleQuery(Search_Query $query, $content)
	{
		$table = TikiDb::get()->table('tiki_extwiki');
		$tikis = $table->fetchAll($table->all(), ['indexname' => $table->not('')]);

		foreach ($tikis as $tiki) {
			$sub = $this->addExternalTiki($query, $tiki['indexname'], $this->extractBaseUrl($tiki['extwiki']), json_decode($tiki['groups']) ?: []);
			$sub->filterContent($content, ['title', 'contents']);
		}
	}

	private function addExternalTiki($query, $indexName, $baseUrl, array $applyAs)
	{
		$sub = new Search_Query;
		$this->unified->initQueryBase($sub, false);

		if (empty($applyAs)) {
			$this->unified->initQueryPermissions($sub);
		} else {
			$sub->filterPermissions($applyAs);
		}

		$sub->applyTransform(new Search_Elastic_Transform_UrlPrefix($baseUrl));

		$query->includeForeign($indexName, $sub);

		return $sub;
	}

	private function extractBaseUrl($url)
	{
		$slash = strrpos($url, '/');
		return substr($url, 0, $slash + 1);
	}
}

