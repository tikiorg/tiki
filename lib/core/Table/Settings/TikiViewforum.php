<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: TikiAdminusers.php 53211 2014-11-27 19:36:24Z lindonb $

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
	protected $ts = array(
		'filters' => array(
			'external' => array(
				0 => array(
					'type' => 'dropdown',
					'options' => array(
						'Show archived' => 'show_archived=y',
					),
				),
			),
		),
		'ajax' => array(
			'offset' => 'comments_offset',
			'sortparam' => 'thread_sort_mode',
			'numrows' => 'comments_per_page',
			'url' => array(
				'file' => 'tiki-view_forum.php',
			),
		),
		'columns' => array(
			'#checkbox' => array(
				'sort' => array(
					'type' => false,
					'group' => 'false'
				),
				'filter' => array(
					'type' => false,
				),
				'resizable' => false,
				'priority' => 'critical',
			),
			'#type' => array(
				'sort' => array(
					'type' => 'image',
					'ajax' =>'type',
					'group' => 'word',
				),
				'filter' => array(
					'type' => 'dropdown',
					'ajax' => 'filter_type',
					'options' => array(
						'n|Normal' => '',
						'a|Announce' => '',
						'h|Hot' => '',
						's|Sticky' => '',
					),
				),
				'priority' => 6,
			),
			'#smiley' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#title' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'title',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 'critical',
			),
			'#replies' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' => 'replies',
					'group' => 'number-50'
				),
				'filter' => array(
					'type' => 'dropdown',
					'ajax' => 'reply_state',
					'options' => array(
						'none|No replies' => '',
					),
				),
				'priority' => 5,
			),
			'#hits' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' => 'hits',
					'group' => 'number-50'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 3,
			),
			'#rating' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#rating2' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#rating3' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#average' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' => 'average',
					'group' => 'number-50'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#lastpost' => array(
				'sort' => array(
					'type' => 'isoDate',
					'ajax' => 'lastPost',
					'group' => 'date-year'
				),
				'filter' => array(
					'type' => 'dropdown',
					'ajax' => 'time_control',
					'options' => array(
						'3600|Last hour' => '',
						'86400|Last 24 hours' => '',
						'172800|Last 48 hours' => '',
					),
				),
				'priority' => 4,
			),
			'#poster' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'userName',
					'group' => 'word'
				),
				'filter' => array(
					'type' => 'text',
					'ajax' => 'poster',
				),
				'priority' => 2,
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
			'#atts' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#lang' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#category' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
		),
	);
}