<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Settings_TikiListpages
 *
 * Tablesorter settings for the table listing users at tiki-listpages.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiListpages extends Table_Settings_Standard
{
	protected $ts = [
		'filters' => [
			'external' => [
				0 => [
					'type' => 'dropdown',
					'options' => [
						'Orphan pages' => 'findfilter_orphan=page_orphans',
						'Pages not in a structure' => 'findfilter_orphan=structure_orphans',
					],
				],
			],
		],
		'ajax' => [
			'url' => [
				'file' => 'tiki-listpages.php',
			],
			'numrows' => 'maxRecords',
		],
		'columns' => [
			'#checkbox' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'resizable' => false,
				'priority' => 'critical',
			],
			'#pageid' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'page_id',
					'group' => 'number-10'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#pagename' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'pageName',
					'group' => 'letter'
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'find',
				],
				'priority' => 'critical',
			],
			'#hits' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'hits',
					'group' => 'number-1000'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 5,
			],
			'#lastmodif' => [
				'sort' => [
					'type' => 'isoDate',
					'ajax' => 'lastModif',
					'group' => 'date-year'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 4,
			],
			'#creator' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'creator',
					'group' => 'letter'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 3,
			],
			'#lastauthor' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'user',
					'group' => 'letter'
				],
				'filter' => [
					'type' => false,//function doesn't allow for filtering on last author
				],
				'priority' => 3,
			],
			'#version' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'version',
					'group' => 'number-100'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 1,
			],
			'#status' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'flag',
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 5,
			],
			'#versions' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'versions',
					'group' => 'number-100'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#links' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'links',
					'group' => 'number-10'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#backlinks' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'backlinks',
					'group' => 'number-10'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#size' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'page_size',
					'group' => 'number-10'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 3,
			],
			'#language' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'lang',
					'group' => 'word'
				],
				'filter' => [
					'type' => 'dropdown',
					'ajax' => 'lang',
				],
				'priority' => 2,
			],
			'#categories' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => 'dropdown',
					'ajax' => 'categ_ts',
				],
				'priority' => 6,
			],
			'#catpaths' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => 'dropdown',
					'ajax' => 'categPath_ts',
				],
				'priority' => 6,
			],
			'#rating' => [
				'sort' => [
					'type' => 'digits',
					'ajax' => 'rating',
					'group' => 'number-10'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#actions' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 1,
			],
		],
	];

	/**
	 * Manipulate table-specific settings as needed.
	 *
	 * @return array|null
	 */
	protected function getTableSettings()
	{
		global $prefs;
		if ($prefs['wiki_list_comment'] === 'y') {
			$this->ts['columns']['#lastmodif']['sort']['type'] = 'text';
			$this->ts['columns']['#lastmodif']['sort']['group'] = 'word';
		}

		if ($prefs['feature_listorphanPages'] !== 'y') {
			unset($this->ts['filters']['external'][0]['options']['Orphan pages']);
		}
		if ($prefs['feature_wiki_structure'] !== 'y' || $prefs['feature_listorphanStructure'] !== 'y') {
			unset($this->ts['filters']['external'][0]['options']['Pages not in a structure']);
		}

		return $this->ts;
	}
}
