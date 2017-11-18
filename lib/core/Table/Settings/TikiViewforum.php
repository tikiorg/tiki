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
 * Class Table_Settings_TikiViewforum
 *
 * Tablesorter settings for the table listing a forum's posts at tiki-view_forum.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiViewforum extends Table_Settings_Standard
{
	protected $ts = [
		'filters' => [
			'external' => [
				0 => [
					'type' => 'dropdown',
					'options' => [
						'Show archived' => 'show_archived=y',
					],
				],
			],
		],
		'ajax' => [
			'offset' => 'comments_offset',
			'sortparam' => 'thread_sort_mode',
			'numrows' => 'comments_per_page',
			'url' => [
				'file' => 'tiki-view_forum.php',
			],
		],
		'columns' => [
			'#checkbox' => [
				'sort' => [
					'type' => false,
					'group' => 'false'
				],
				'filter' => [
					'type' => false,
				],
				'resizable' => false,
				'priority' => 'critical',
			],
			'#type' => [
				'sort' => [
					'type' => 'image',
					'ajax' => 'type',
					'group' => 'word',
				],
				'filter' => [
					'type' => 'dropdown',
					'ajax' => 'filter_type',
					'options' => [
						'n|Normal' => '',
						'a|Announce' => '',
						'h|Hot' => '',
						's|Sticky' => '',
					],
				],
				'priority' => 6,
			],
			'#smiley' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#title' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'title',
					'group' => 'letter'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 'critical',
			],
			'#replies' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'replies',
					'group' => 'number-50'
				],
				'filter' => [
					'type' => 'dropdown',
					'ajax' => 'reply_state',
					'options' => [
						'none|No replies' => '',
					],
				],
				'priority' => 5,
			],
			'#hits' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'hits',
					'group' => 'number-50'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 3,
			],
			'#rating' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#rating2' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#rating3' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#average' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'average',
					'group' => 'number-50'
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#lastpost' => [
				'sort' => [
					'type' => 'isoDate',
					'ajax' => 'lastPost',
					'group' => 'date-year'
				],
				'filter' => [
					'type' => 'dropdown',
					'ajax' => 'time_control',
					'options' => [
						'3600|Last hour' => '',
						'86400|Last 24 hours' => '',
						'172800|Last 48 hours' => '',
					],
				],
				'priority' => 4,
			],
			'#poster' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'userName',
					'group' => 'word'
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'poster',
				],
				'priority' => 2,
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
			'#atts' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#lang' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#category' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
		],
	];
}
