<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search\Federated\ManifoldCfIndex;

class Services_Search_ManifoldController
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_search');
		Services_Exception_Denied::checkGlobal('tiki_p_admin');
	}

	function action_check($input)
	{
		$lib = TikiLib::lib('federatedsearch');
		$unified = TikiLib::lib('unifiedsearch');

		$instances = [];
		$indices = $lib->getIndices();

		foreach ($indices as $indexName => $index) {
			if ($index instanceof ManifoldCfIndex) {
				$valid = false;
				$type = $index->getType();
				$mapping = $unified->getElasticIndexInfo($indexName);
				$properties = false;

				if ($mapping) {
					$first = key($mapping);
					if (isset($mapping->$first->mappings->$type->properties)) {
						$properties = $mapping->$first->mappings->$type->properties;
					}
				}

				if (isset($properties->file->type)) {
					$valid = 'attachment' === $properties->file->type;
				}

				$instances[] = [
					'name' => $indexName,
					'type' => $type,
					'indexExists' => ! empty($mapping),
					'typeExists' => ! empty($properties),
					'valid' => $valid,
				];
			}
		}

		return array(
			'title' => tr('ManifoldCF Configuration Check'),
			'instances' => $instances,
		);
	}

	function action_create_index($input)
	{
		global $prefs;

		$index = $input->index->word();
		$type = $input->type->word();
		$location = $input->location->url() ?: $prefs['unified_elastic_url'];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$lib = TikiLib::lib('federatedsearch');
			try {
				$lib->createIndex($location, $index, $type, [
					'file' => [
						'type' => 'attachment',
					],
				]);

				return [
					'FORWARD' => [
						'action' => 'check',
					],
				];
			} catch (Search_Elastic_MappingException $e) {
				if ($e->getType() == 'attachment') {
					throw new Services_Exception_NotAvailable('Attachment field plugin not installed on ElasticSearch server.');
				} else {
					throw $e;
				}
			}
		}

		return [
			'title' => tr('Create ManifoldCF Index'),
			'location' => $location,
			'index' => $index,
			'type' => $type,
		];
	}
}
