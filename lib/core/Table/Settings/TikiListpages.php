<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
	protected $ts = array(
		'filters' => array(
			'external' => array(
				0 => array(
					'type' => 'dropdown',
					'options' => array(
						'Orphan pages' => 'findfilter_orphan=page_orphans',
						'Pages not in a structure' => 'findfilter_orphan=structure_orphans',
					),
				),
			),
		),
		'ajax' => array(
			'url' => array(
				'file' => 'tiki-listpages.php',
			),
			'numrows' => 'maxRecords',
		),
		'columns' => array(
			'#checkbox' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'resizable' => false,
				'priority' => 'critical',
			),
			'#pageid' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' => 'page_id',
					'group' => 'number-10'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#pagename' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' =>'pageName',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => 'text',
					'ajax' => 'find',
				),
				'priority' => 'critical',
			),
			'#hits' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'hits',
					'group' => 'number-1000'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 5,
			),
			'#lastmodif' => array(
				'sort' => array(
					'type' => 'isoDate',
					'ajax' => 'lastModif',
					'group' => 'date-year'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 4,
			),
			'#creator' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'creator',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 3,
			),
			'#lastauthor' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'user',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => false,//function doesn't allow for filtering on last author
				),
				'priority' => 3,
			),
			'#version' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'version',
					'group' => 'number-100'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 1,
			),
			'#status' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'flag',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 5,
			),
			'#versions' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'versions',
					'group' => 'number-100'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'links' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'links',
					'group' => 'number-10'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#backlinks' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'backlinks',
					'group' => 'number-10'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#size' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'size',
					'group' => 'number-10'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 3,
			),
			'#language' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'lang',
					'group' => 'word'
				),
				'filter' => array(
					'type' => 'dropdown',
					'ajax' => 'lang',
				),
				'priority' => 2,
			),
			'#categories' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => 'dropdown',
					'ajax' => 'categ_ts',
				),
				'priority' => 6,
			),
			'#catpaths' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => 'dropdown',
					'ajax' => 'categPath_ts',
				),
				'priority' => 6,
			),
			'#rating' => array(
				'sort' => array(
					'type' => 'digits',
					'ajax' => 'rating',
					'group' => 'number-10'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#actions' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 1,
			),
		),
	);

	/**
	 * Manipulate table-specific settings as needed.
	 *
	 * @return array|null
	 */
	protected function getTableSettings()
	{
		//change columns based on prefs to be consistent with tiki-adminusers.tpl
		global $prefs;
		$perms = Perms::get();
		//
		//set initial sort order based on user preferences or default
		$field = !empty($prefs['wiki_list_sortorder']) ? $prefs['wiki_list_sortorder'] : 'lastmodif';
		$dir = !empty($prefs['wiki_list_sortdirection']) ? $prefs['wiki_list_sortdirection'] : 'desc';
		$sortfield = array(
			'pageName'  => '#pagename',
			'lastModif' => '#lastmodif',
			'creator'   => '#creator',
			'hits'      => '#hits',
			'user'      => '#lastauthor',
			'page_size' => '#size',
		);
		$this->ts['columns'][$sortfield[$field]]['sort']['dir'] = $dir;

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

