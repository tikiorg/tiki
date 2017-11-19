<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_rating_list()
{
	return [
		'rating_advanced' => [
			'name' => tra('Advanced Rating'),
			'description' => tra('Rating system allowing for options and calculation method to be configured.'),
			'type' => 'flag',
			'help' => 'Advanced+Rating',
			'default' => 'n',
			'admin' => 'rating',
			'tags' => ['advanced'],
		],
		'rating_recalculation' => [
			'name' => tra('Rating recalculation mode'),
			'description' => tra('Determines when and how rating aggregates are recalculated. Depending on the site load, some options may be preferred to others. On large-volume sites, it should be done as a cron job. On vote recalculation, there may be inaccuracies if rating calculation is time-dependent.'),
			'type' => 'list',
			'options' => [
				'vote' => tra('Recalculate on vote'),
				'randomload' => tra('Randomly recalculate oldest ratings (on load)'),
				'randomvote' => tra('Randomly recalculate oldest ratings (on vote)'),
				'period' => tra('Cron job (must be set manually)'),
				'indexing' => tr('Before any attempt to re-index the object'),
			],
			'default' => 'vote',
		],
		'rating_recalculation_odd' => [
			'name' => tra('Recalculation odds (1 in X)'),
			'description' => tra('Dice roll performed on every request. When successful, will recalculate a certain number of votes.'),
			'type' => 'text',
			'units' => tra('votes'),
			'size' => 5,
			'filter' => 'digits',
			'default' => '100',
		],
		'rating_recalculation_count' => [
			'name' => tra('Recalculation count'),
			'description' => tra('How many ratings should be recalculated when odds strike.'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'units' => tra('ratings'),
			'default' => '100',
		],
		'rating_smileys' => [
			'name' => tr('Smiley Ratings'),
			'description' => tr('Displays smiley faces for a simple ratings system with an easy-to-use user interface, similar to emoticons. Works with 0-11 only, depending on the configuration.'),
			'type' => 'flag',
			'options' => [
				'' => tr('Disabled'),
				'y' => tr('Enabled'),
			],
			'default' => ''
		],
		'rating_results_detailed' => [
			'name' => tr('Detailed Rating Results'),
			'description' => tr('Displays a table with the result count and percentage per rating option.'),
			'type' => 'flag',
			'options' => [
				'' => tr('Disabled'),
				'y' => tr('Enabled'),
			],
			'default' => ''
		],
		'rating_options_reversed' => [
			'name' => tr('Reversed Rating Options'),
			'description' => tr('Displays the ratings options in reverse.'),
			'type' => 'flag',
			'options' => [
				'' => tr('Disabled'),
				'y' => tr('Enabled'),
			],
			'default' => ''
		],
		'rating_results_detailed_percent' => [
			'name' => tr('Include percentages in the detailed rating results'),
			'description' => tr('Include percentages in the detailed rating results. Otherwise, only count numbers and bars are shown.'),
			'type' => 'flag',
			'options' => [
				'' => tr('Disabled'),
				'y' => tr('Enabled'),
			],
			'default' => ''
		],
		'rating_default_options' => [
			'name' => tra('Default rating options'),
			'description' => tra('List of options available.'),
			'type' => 'text',
			'default' => "0,1,2,3,4",
		],
		'rating_allow_multi_votes' => [
			'name' => tra('Multiple votes per user'),
			'description' => tra('Allow the user to cast multiple votes for the same object.'),
			'type' => 'flag',
			'options' => [
				'' => tr('Disabled'),
				'y' => tr('Enabled'),
			],
			'default' => ''
		],
	];
}
