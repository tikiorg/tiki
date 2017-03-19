<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class Services_Edit_ListPluginHelper
 *
 * Definition of what the list plugin supports
 *
 * Unfortunately not derivable from the underlying code so this will need to be kept up to date when
 * adding new formatters, filters etc to plugin list and the unified search code
 *
 */
class Services_Edit_ListPluginHelper
{
	static function getDefinition()
	{
		return [
			'display' =>
				[
					'name' => '',
					'default' => '',
					'format' =>
						[
							'categorylist' =>
								[
									'requiredParents' =>
										[
											0 => 'categories',
										],
									'excludeParents' =>
										[
											0 => 'categories',
										],
									'singleList' =>
										[
											0 => 'y',
											1 => 'n',
										],
									'separator' => '',
									'levelSeparator' => '',
									'useFullPath' =>
										[
											0 => 'y',
											1 => 'n',
										],
								],
							'date' =>
								[
									'dateFormat' => 'dateformat',
								],
							'datetime' =>
								[
									'dateFormat' => 'dateformat',
								],
							'imagegrabber' =>
								[
									'max' => 0,
									'height' => 0,
									'width' => 0,
									'smartcrop' =>
										[
											0 => 'y',
											1 => 'n',
										],
									'content_type' =>
										[
											0 => 'html',
											1 => 'forumpost',
										],
								],
							'objectlink' =>
								[
								],
							'plain' =>
								[
								],
							'reference' =>
								[
									'type' => 'object_type',
									'separator' => '',
								],
							'snippet' =>
								[
									'length' => 0,
									'suffix' => '',
								],
							'sorthandle' =>
								[
								],
							'timeago' =>
								[
								],
							'trackerrender' =>
								[
								],
							'urlencode' =>
								[
								],
							'wikiplugin' =>
								[
									'name' => '',
									'default' => '',
								],
						],
					'list_mode' =>
						[
							0 => 'y',
							1 => 'n',
						],
					'pagetitle' =>
						[
							0 => 'y',
							1 => 'n',
						],
					'editable' =>
						[
							0 => 'inline',
							1 => 'block',
						],
				],
			'filter' =>
				[
					'categories' => 'categories',
					'content' =>
						[
							0 => '',
							1 =>
								[
									'field' => 'field',
								],
						],
					'contributors' =>
						[
						],
					'deepcategories' => 'categories',
					'distance' =>
						[
							0 => '',
							1 =>
								[
									'lon' => 0.0,
									'lat' => 0.0,
								],
						],
					'editable' =>
						[
							0 => 'y',
							1 => 'n',
						],
					'exact' =>
						[
							0 => '',
							1 =>
								[
									'field' => '',
								],
						],
					'favorite' => 'user',
					'language' => '',
					'multivalue' =>
						[
						],
					'nottype' =>
						[
						],
					'personalize' =>
						[
						],
					'range' =>
						[
							'from' => 'datetime',
							'to' => 'datetime',
						],
					'relation' =>
						[
							'objecttype' => 'object_type',
							'qualifier' => '',
						],
					'textrange' =>
						[
							'from' => '',
							'to' => '',
						],
					'type' => 'object_type',
				],
			'format' =>
				[
					'name' => '',
				],
			'list' =>
				[
					'max' =>
						[
						],
				],
			'pagination' =>
				[
					'offset_arg' => '',
					'offset_jsvar' => '',
					'onclick' => '',
					'max' => 0,
					'sort_arg' => '',
				],
			'output' =>
				[
					'template' =>
						[
							'input' => '',
							'table' =>
								[
									'column' =>
										[
										],
									'tablesorter' =>
										[
										],
								],
							'medialist' =>
								[
									'icon' =>
										[
										],
									'body' =>
										[
										],
								],
							'carousel' =>
								[
									'carousel' =>
										[
											'interval' => 0,
											'wrap' => 0,
											'pause' =>
												[
													0 => 'hover',
												],
											'id' => '',
										],
								],
							'count' =>
								[
								],
						],
					'sort' =>
						[
							'mode' => '',
						],
					'group' =>
						[
							'boost' =>
								[
								],
						],
				],
		];

	}
}

