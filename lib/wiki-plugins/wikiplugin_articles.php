<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_articles_info()
{
	global $prefs;
	return [
		'name' => tra('Article List'),
		'documentation' => 'PluginArticles',
		'description' => tra('Display multiple articles'),
		'prefs' => [ 'feature_articles', 'wikiplugin_articles' ],
		'iconname' => 'articles',
		'tags' => [ 'basic' ],
		'introduced' => 1,
		'params' => [
			'usePagination' => [
				'required' => false,
				'name' => tra('Use Pagination'),
				'description' => tr('Activate pagination when the articles list is long. Default is %0', '<code>n</code>'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '1',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				],
			],
			'max' => [
				'required' => false,
				'name' => tra('Maximum Displayed'),
				'description' => tr('The number of articles to display in the list (use %0 to show all)', '<code>-1</code>'),
				'filter' => 'int',
				'since' => '1',
				'default' => $prefs['maxRecords'],
			],
			'topic' => [
				'required' => false,
				'name' => tra('Topic Name Filter'),
				'description' => tra('Filter the list of articles by topic. Example: ') . '<code>[!]topic+topic+topic</code>',
				'filter' => 'text',
				'since' => '1',
				'default' => '',
			],
			'topicId' => [
				'required' => false,
				'name' => tra('Topic ID Filter'),
				'description' => tra('Filter the list of articles by topic ID. Example: ') . '<code>[!]topicId+topicId+topicId</code>',
				'filter' => 'text',
				'accepted' => tra('Valid topic IDs'),
				'default' => '',
				'profile_reference' => 'article_topic',
				'since' => '2.0',
			],
			'type' => [
				'required' => false,
				'name' => tra('Type Filter'),
				'description' => tra('Filter the list of articles by types. Example: ') . '<code>[!]type+type+type</code>',
				'filter' => 'text',
				'since' => '1',
				'accepted' => tra('Valid article types'),
				'default' => '',
				'profile_reference' => 'article_type',
			],
			'categId' => [
				'required' => false,
				'name' => tra('Category ID'),
				'description' => tra('List of category IDs, separated by "%0". Only articles in all these categories are
					listed', '<code>|</code>'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'category',
				'since' => '1',
				'separator' => '|',
			],
			'lang' => [
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('List only articles in this language'),
				'filter' => 'lang',
				'since' => '1',
				'default' => '',
			],
			'sort' => [
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tr('The column and order of the sort in %0columnName_asc%1 or %0columnName_desc%1 format.
					Defaults to %0publishDate_desc%1 (other column examples are %0title%1, %0lang%1, %0articleId%1,
					%0authorName%1 & %0topicName%1). Use "random" to have random items.', '<code>', '</code>'),
				'filter' => 'word',
				'default' => 'publishDate_desc',
				'since' => '2.0',
				'accepted' => tra('random or column names to add _asc _desc to: ')
					. 'created, author, title, publishDate, expireDate, articleId, topline, subtitle, lang, linkto, authorName, topicId, topicName, state, size, heading, body, isfloat, useImage, image_name, image_caption, image_type, image_size, image_x, image_y, image_data, list_image_x, list_image_y, nbreads, votes, points, type, rating, ispublished'],
			'order' => [
				'required' => false,
				'name' => tra('Specific order'),
				'description' => tra('List of ArticleId that must appear in this order if present'),
				'filter' => 'digits',
				'separator' => '|',
				'default' => '',
				'since' => '9.0',
			],
			'articleId' => [
				'required' => false,
				'name' => tra('Only these articles'),
				'description' => tr('List of article IDs to display, separated by "%0"', '<code>|</code>'),
				'filter' => 'digits',
				'separator' => '|',
				'default' => '',
				'profile_reference' => 'article',
				'since' => '9.0',
			],
			'notArticleId' => [
				'required' => false,
				'name' => tra('Not these articles'),
				'description' => tra('List of article IDs to not display, separated by "%0"', '<code>|</code>'),
				'filter' => 'digits',
				'separator' => '|',
				'default' => '',
				'profile_reference' => 'article',
				'since' => '5.0',
			],
			'quiet' => [
				'required' => false,
				'name' => tra('Quiet'),
				'description' => tra('Whether to not report when there are no articles (no reporting by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '1',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'titleonly' => [
				'required' => false,
				'name' => tra('Title Only'),
				'description' => tra('Whether to only show the title of the articles (not set to title only by default)'),
				'filter' => 'alpha',
				'since' => '1',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'fullbody' => [
				'required' => false,
				'name' => tra('Show Article Body'),
				'description' => tra('Whether to show the body of the articles instead of the heading (not set by default).'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '5',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'start' => [
				'required' => false,
				'name' => tra('Starting Article'),
				'description' => tra('The article number that the list should start with (starts with first article by
					default)') . '. ' . tra('This will not work if Pagination is used.'),
				'filter' => 'int',
				'since' => '1',
				'default' => 0,
			],
			'dateStart' => [
				'required' => false,
				'name' => tra('Start Date'),
				'description' => tra('Earliest date to select articles from.') . tr(' (%0YYYY-MM-DD%1)', '<code>', '</code>'),
				'filter' => 'date',
				'default' => '',
				'since' => '5.0',
			],
			'dateEnd' => [
				'required' => false,
				'name' => tra('End date'),
				'description' => tra('Latest date to select articles from.') . tr(' (%0YYYY-MM-DD%1)', '<code>', '</code>'),
				'filter' => 'date',
				'default' => '',
				'since' => '5.0',
			],
			'periodQuantity' => [
				'required' => false,
				'name' => tra('Period quantity'),
				'description' => tr('Numeric value to display only last articles published within a user defined
					time-frame. Used in conjunction with the next parameter "Period unit", this parameter indicates how
					many of those units are to be considered to define the time frame. If this parameter is set,
					"Start Date" and "End Date" are ignored.'),
				'filter' => 'digits',
				'since' => '1',
				'default' => '',
			],
			'periodUnit' => [
				'required' => false,
				'name' => tra('Period unit'),
				'description' => tr('Time unit used with "Period quantity"'),
				'filter' => 'word',
				'since' => '1',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tr('Hour'), 'value' => 'hour'],
					['text' => tr('Day'), 'value' => 'day'],
					['text' => tr('Week'), 'value' => 'week'],
					['text' => tr('Month'), 'value' => 'month'],
				],
			],
			'overrideDates' => [
				'required' => false,
				'name' => tra('Override Dates'),
				'description' => tra('Whether to comply with the article type\'s "show before publish" and "show after expiration" settings (not complied with by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '1',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'containerClass' => [
				'required' => false,
				'name' => tra('Containing class'),
				'description' => tr(
					'CSS class to add to the containing "div.article" (default: "%0")',
					'<code>wikiplugin_articles</code>'
				),
				'filter' => 'text',
				'since' => '1',
				'accepted' => tra('Valid CSS class'),
				'default' => 'wikiplugin_articles',
			],
			'largefirstimage' => [
				'required' => false,
				'name' => tra('Large First Image'),
				'description' => tr('If set to %0 (Yes), the first image will be displayed with the dimension used to
					view of the article', '<code>y</code>'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '6.0',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'urlparam' => [
				'required' => false,
				'name' => tra('Additional URL parameter for the link to read the article'),
				'filter' => 'text',
				'default' => '',
				'since' => '6.0',
			],
			'actions' => [
				'required' => false,
				'name' => tra('Show actions (buttons and links)'),
				'description' => tra('Whether to show the buttons and links to do actions on each article (for the
					actions you have permission to do'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '6.1',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'translationOrphan' => [
				'required' => false,
				'name' => tra('No translation'),
				'description' => tra('User- or pipe-separated list of two-letter language codes for additional languages
					to display. List pages with no language or with a missing translation in one of the language'),
				'filter' => 'alpha',
				'separator' => '|',
				'since' => '1',
				'default' => '',
			],
			'useLinktoURL' => [
				'required' => false,
				'name' => tra('Use Source URL'),
				'description' => tra('Use the external source URL as link for articles.'),
				'filter' => 'alpha',
				'since' => '1',
				'default' => 'n',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
		],
	];
}

function wikiplugin_articles($data, $params)
{
	global $prefs, $tiki_p_read_article, $tiki_p_articles_read_heading, $pageLang;
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$artlib = TikiLib::lib('art');
	$default = ['max' => $prefs['maxRecords'], 'start' => 0, 'usePagination' => 'n', 'topicId' => '', 'topic' => '', 'sort' => 'publishDate_desc', 'type' => '', 'lang' => '', 'quiet' => 'n', 'categId' => '', 'largefirstimage' => 'n', 'urlparam' => '', 'actions' => 'n', 'translationOrphan' => '', 'headerLinks' => 'n', 'showtable' => 'n', 'useLinktoURL' => 'n'];
	$auto_args = ['lang', 'topicId', 'topic', 'sort', 'type', 'lang', 'categId'];
	$params = array_merge($default, $params);

	extract($params, EXTR_SKIP);
	$filter = '';
	if ($prefs['feature_articles'] != 'y') {
		//	the feature is disabled or the user can't read articles, not even article headings
		return("");
	}

	$urlnext = '';
	if ($usePagination == 'y') {
		//Set offset when pagniation is used
		if (! isset($_REQUEST["offset"])) {
			$start = 0;
		} else {
			$start = $_REQUEST["offset"];
		}

		foreach ($auto_args as $arg) {
			if (! empty($$arg)) {
				$paramsnext[$arg] = $$arg;
			}
		}
		$paramsnext['_type'] = 'absolute_path';
		$smarty->loadPlugin('smarty_function_query');
		$urlnext = smarty_function_query($paramsnext, $smarty);
	}

	$smarty->assign_by_ref('quiet', $quiet);
	$smarty->assign_by_ref('urlparam', $urlparam);
	$smarty->assign_by_ref('urlnext', $urlnext);
	$smarty->assign_by_ref('useLinktoURL', $useLinktoURL);

	if (! isset($containerClass)) {
		$containerClass = 'wikiplugin_articles';
	}
	$smarty->assign('container_class', $containerClass);

	$dateStartTS = 0;
	$dateEndTS = 0;

	// if a period of time is set, date start and end are ignored
	if (isset($periodQuantity)) {
		switch ($periodUnit) {
			case 'hour':
				$periodUnit = 3600;
				break;
			case 'day':
				$periodUnit = 86400;
				break;
			case 'week':
				$periodUnit = 604800;
				break;
			case 'month':
				$periodUnit = 2628000;
				break;
			default:
				break;
		}

		if (is_int($periodUnit)) {
			$dateStartTS = $tikilib->now - ($periodQuantity * $periodUnit);
			$dateEndTS = $tikilib->now;
		}
	} else {
		if (isset($dateStart)) {
			$dateStartTS = strtotime($dateStart);
		}

		if (isset($dateEnd)) {
			$dateEndTS = strtotime($dateEnd);
		}
	}

	if (isset($fullbody) && $fullbody == 'y') {
		$smarty->assign('fullbody', 'y');
	} else {
		$smarty->assign('fullbody', 'n');
		$fullbody = 'n';
	}
	$smarty->assign('largefirstimage', $largefirstimage);
	if (! isset($overrideDates)) {
		$overrideDates = 'n';
	}

	if (! empty($translationOrphan)) {
		$filter['translationOrphan'] = $translationOrphan;
	}
	if (! empty($articleId)) {
		$filter['articleId'] = $articleId;
	}
	if (! empty($notArticleId)) {
		$filter['notArticleId'] = $notArticleId;
	}

	if (! is_array($categId) || count($categId) == 0) {
		$categIds = '';
	} elseif (is_array($categId) && count($categId) == 1) {
		// For performance reasons, if there is only one value, the SQL query should not return IN () as it does with arrays
		// So we send a single value instead of a single-value array
		$categIds = $categId[0];
	} else {
		// We want the list of articles which are in all categories
		$categIds = [ 'AND' => $categId];
	}

	$listpages = $artlib->list_articles($start, $max, $sort, '', $dateStartTS, $dateEndTS, 'admin', $type, $topicId, 'y', $topic, $categIds, '', '', $lang, '', '', ($overrideDates == 'y'), 'y', $filter);
	if ($prefs['feature_multilingual'] == 'y' && empty($translationOrphan)) {
		$multilinguallib = TikiLib::lib('multilingual');
		$listpages['data'] = $multilinguallib->selectLangList('article', $listpages['data'], $pageLang);
		foreach ($listpages['data'] as &$article) {
			$article['translations'] = $multilinguallib->getTranslations('article', $article['articleId'], $article["title"], $article['lang']);
		}
	}

	for ($i = 0, $icount_listpages = count($listpages["data"]); $i < $icount_listpages; $i++) {
		$listpages["data"][$i]["parsed_heading"] = TikiLib::lib('parser')->parse_data(
			$listpages["data"][$i]["heading"],
			[
				'min_one_paragraph' => true,
				'is_html' => $artlib->is_html($listpages["data"][$i], true),
			]
		);
		if ($fullbody == 'y') {
			$listpages["data"][$i]["parsed_body"] = TikiLib::lib('parser')->parse_data(
				$listpages["data"][$i]["body"],
				[
					'min_one_paragraph' => true,
					'is_html' => $artlib->is_html($listpages["data"][$i]),
				]
			);
		}
		$comments_prefix_var = 'article:';
		$comments_object_var = $listpages["data"][$i]["articleId"];
		$comments_objectId = $comments_prefix_var . $comments_object_var;
		$listpages["data"][$i]["comments_cant"] = TikiLib::lib('comments')->count_comments($comments_objectId);
		//print_r($listpages["data"][$i]['title']);
	}

	$topics = $artlib->list_topics();
	$smarty->assign_by_ref('topics', $topics);

	if (empty($topicId)) {
		$topicId = '';
	}
	if (empty($type)) {
		$type = '';
	}

	if (! empty($topic) && ! strstr($topic, '!') && ! strstr($topic, '+')) {
		$smarty->assign_by_ref('topic', $topic);
	} elseif (! empty($topicId) &&  is_numeric($topicId)) {
		$smarty->assign_by_ref('topicId', $topicId);
		if (! empty($listpages['data'][0]['topicName'])) {
			$smarty->assign_by_ref('topic', $listpages['data'][0]['topicName']);
		} else {
			$topic_info = $artlib->get_topic($topicId);
			if (isset($topic_info['name'])) {
				$smarty->assign_by_ref('topic', $topic_info['name']);
			}
		}
	} elseif (empty($topicId)) {
		$smarty->assign_by_ref('topicId', $topicId);
	}
	if (! empty($type) && ! strstr($type, '!') && ! strstr($type, '+')) {
		$smarty->assign_by_ref('type', $type);
	} elseif (empty($type)) {
		$smarty->assign_by_ref('type', $type);
	}

	if ($usePagination == 'y') {
		$smarty->assign('maxArticles', $max);
		$smarty->assign_by_ref('offset', $start);
		$smarty->assign_by_ref('cant', $listpages['cant']);
	}
	if (! empty($order)) {
		foreach ($listpages['data'] as $i => $article) {
			$memo[$article['articleId']] = $i;
		}
		foreach ($order as $articleId) {
			if (isset($memo[$articleId])) {
				$list[] = $listpages['data'][$memo[$articleId]];
			}
		}
		foreach ($listpages['data'] as $i => $article) {
			if (! in_array($article['articleId'], $order)) {
				$list[] = $article;
			}
		}
		$smarty->assign_by_ref('listpages', $list);
	} else {
		$smarty->assign_by_ref('listpages', $listpages["data"]);
	}
	$smarty->assign('usePagination', $usePagination);
	$smarty->assign_by_ref('actions', $actions);
	$smarty->assign('headerLinks', $headerLinks);

	if (isset($titleonly) && $titleonly == 'y') {
		return "~np~ " . $smarty->fetch('tiki-view_articles-titleonly.tpl') . " ~/np~";
	} else {
		return "~np~ " . $smarty->fetch('tiki-view_articles.tpl') . " ~/np~";
	}
	//return str_replace("\n","",$smarty->fetch('tiki-view_articles.tpl')); // this considers the hour in the header like a link
}
