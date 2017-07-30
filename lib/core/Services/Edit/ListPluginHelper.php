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
			'filter' => [
				'icon' => 'listgui_filter',
				'params' => [
					'categories' => [
						'type' => 'categories',
					],
					'contributors' => [
						'type' => 'text',
					],
					'deepcategories' => [
						'type' => 'categories',
					],
					'distance' => [
						'type' => 'text',
						'params' => [
							'lon' => [
								'type' => 'number',
							],
							'lat' => [
								'type' => 'number',
							],
						],
					],
					'favorite' => [
						'type' => 'user',
					],
					'field' => [
						'type' => 'field',
						'params' => [
							'content' => [
								'type' => 'text',
							],
							'exact' => [
								'type' => 'text',
							],
							'editable' => [	// n.b. tracker fields only
								'type' => 'text',
								'options' => [
									// taken from current usages of \Tracker\Filter\Collection::addNew
									'all-of' => [],
									'all-of-checkboxes' => [],
									'any-of' => [],
									'any-of-checkboxes' => [],
									'content' => [],
									'dropdown' => [],
									'exact' => [],
									'fulltext' => [],
									'fulltext-current' => [],
									'initial' => [],
									'lookup' => [],
									'object' => [],
									'multiselect' => [],
									'range' => [],
									'selector' => [],
									// TODO add filtering by field type?
									// also fulltext-$lang (for each lang?) and facet-any and facet-all for each facet maybe?
								],
							],
							'multivalue' => [
								'type' => 'text',
							],
						],
					],
					'language' => [
						'type' => 'text',
					],
					'multivalue' => [
						'type' => 'text',
						'params' => [
							'field' => [
								'type' => 'field',
								'required' => true,
							],
						],
					],
					'nottype' => [
						'type' => 'object_type',
					],
					'personalize' => [
						'type' => 'text',    // TODO check
					],
					'range' => [
						'type' => 'field',
						'params' => [
							'from' => [
								'type' => 'datetime',
							],
							'to' => [
								'type' => 'datetime',
							],
							'gap' => [
								'type' => 'datetime',
							],
						],
					],
					'relation' => [
						'params' => [
							'objecttype' => [
								'type' => 'object_type',
							],
							'qualifier' => [
								'type' => 'text',
							],
						],
					],
					'textrange' => [
						'type' => 'field',
						'params' => [
							'from' => [
								'type' => 'text',
							],
							'to' => [
								'type' => 'text',
							],
						],
					],
					'type' => [
						'type' => 'object_type',
					],
				],
			],
			'sort' => [
				'icon' => 'listgui_sort',
				'params' => [
					'mode' => [
						'type' => 'text',
					],
				],
			],
			'pagination' => [
				'icon' => 'listgui_pagination',
				'params' => [
					'max' => [
						'type' => 'number',
					],
					'offset_arg' => [
						'type' => 'text',
					],
					'offset_jsvar' => [
						'type' => 'text',
					],
					'onclick' => [
						'type' => 'text',
					],
					'sort_arg' => [
						'type' => 'text',
					],
				],
			],
			'output' => [
				'icon' => 'listgui_output',
				'params' => [
					'empty' => [],
					'template' => [
						'options' => [
							'input' => [
								'type' => 'text',
							],
							'table' => [
								'plugins' => [
									'column' => [
										'icon' => 'listgui_column',
										'parents' => [
											'output',
										],
										'params' => [
											'field' => [
												'type' => 'field',
												'params' => [
													'sort' => [
														'type' => 'field',
													],
													'label' => [
														'type' => 'text',
													],
													'mode' => [
														'options' => [
															'',
															'raw',
														],
													],
												],
											],
										],
									],
									'tablesorter' => [
										'icon' => 'listgui_tablesorter',
										'parents' => [
											'output',
										],
										'params' => [
											'sortable' => [
												'type' => 'text',
												'params' => [
													'server' => [
														'type' => 'checkbox',
													],
													'sortList' => [
														'type' => 'text',
													],
													'tscolselect' => [
														'type' => 'text',
													],
													'tsfilters' => [
														'type' => 'text',
													],
													'tsfilteroptions' => [
														'type' => 'text',
													],
													'tspaginate' => [
														'type' => 'text',
													],
													'tsortcolumns' => [
														'type' => 'text',
													],
													'tstotaloptions' => [
														'type' => 'text',
													],
													'tstotals' => [
														'type' => 'text',
													],
												],
											],
										],
									],

								],
							],
							'medialist' => [
								'plugins' => [
									'icon' => [
										'icon' => 'listgui_icon',
										'parents' => [
											'output',
										],
										'params' => [
											'field' => [
												'type' => 'field',
											],
										],
									],
									'body' => [
										'icon' => 'listgui_body',
										'parents' => [
											'output',
										],
										'params' => [
											'field' => [
												'type' => 'field',
											],
										],
									],
								],
							],
							'carousel' => [
								'plugins' => [
									'carousel' => [
										'icon' => 'listgui_carousel',
										'parents' => [
											'output',
										],
										'params' => [
											'id' => [
												'type' => 'text',
											],
											'params' => [
												'interval' => [
													'type' => 'number',
												],
												'wrap' => [
													'type' => 'number',
												],
												'pause' => [
													'options' => [
														'',
														'hover',
													],
												],
											],
										],
									],
									'body' => [
										'icon' => 'listgui_body',
										'parents' => [
											'output',
										],
										'params' => [
											'field' => [
												'type' => 'field',
												'params' => [
													'mode' => [
														'options' => [
															'',
															'raw',
														],
													],
												],
											],
										],
									],
									'caption' => [
										'icon' => 'listgui_caption',
										'parents' => [
											'output',
										],
										'params' => [
											'field' => [
												'type' => 'field',
											],
										],
									],
								],
							],
							'count' => [
							],
						],
					],
					'wiki' => [
						'type' => 'pagename',
					],
				],
			],
			'format' => [
				'icon' => 'listgui_format',
				'params' => [
					'name' => [
						'type' => 'text',
					],
				],
			],
			'display' => [
				'icon' => 'listgui_display',
				'parents' => [
					'output',
					'format',
				],
				'params' => [
					'name' => [
						'type' => 'field',
						'params' => [
							'default' => [
								'type' => 'text',
							],
							'format' => [
								'options' => [
									'plain' => [],
									'categorylist' => [
										'params' => [
											'requiredParents' => [
												'type' => 'categories',
											],
											'excludeParents' => [
												'type' => 'categories',
											],
											'singleList' => [
												'type' => 'checkbox',
											],
											'separator' => [
												'type' => 'text',
											],
											'levelSeparator' => [
												'type' => 'text',
											],
											'useFullPath' => [
												'type' => 'checkbox',
											],
										],
									],
									'date' => [
										'params' => [
											'dateFormat' => [
												'type' => 'dateformat',
											],
										],
									],
									'datetime' => [
										'params' => [
											'dateFormat' => [
												'type' => 'dateformat',
											],
										],
									],
									'imagegrabber' => [
										'params' => [
											'max' => [
												'type' => 'number',
											],
											'height' => [
												'type' => 'number',
											],
											'width' => [
												'type' => 'number',
											],
											'smartcrop' => [
												'type' => 'checkbox',
											],
											'content_type' => [
												'options' => [
													'html',
													'forumpost',
												],
											],
										],
									],
									'objectlink' => [],
									'reference' => [
										'params' => [
											'type' => [
												'type' => 'object_type',
											],
											'separator' => [
												'type' => 'text',
											],
										],
									],
									'snippet' => [
										'params' => [
											'length' => [
												'type' => 'number',
											],
											'suffix' => [
												'type' => 'text',
											],
										],
									],
									'sorthandle' => [],
									'timeago' => [],
									'trackerrender' => [],
									'urlencode' => [],
									'wikiplugin' => [
										'params' => [
											'name' => [
												'type' => 'wikiplugin',
											],
											'default' => [
												'type' => 'text',
											],
											// TODO some way of adding the plugin's params too (use wildcard for now)
											'*' => [
												'type' => 'text',
											],
										],
									],
								],
							],
							'list_mode' => [
								'type' => 'checkbox',
							],
							'pagetitle' => [
								'type' => 'checkbox',
							],
							'editable' => [
								'options' => [
									'inline',
									'block',
								],
							],
						],
					],
				],
			],
			'wiki text' => [	// allow wiki text within output and format plugins
				'icon' => 'listgui_wikitext',
				'parents' => [
					'output',
					'format',
				],
				'params' => [
					'empty' => [],
				],
			],
			'group' => [    // what is this?
				'params' => [
					'boost' => [
						'type' => 'number',
					],
				],
			],
			'list' => [
				'params' => [
					'max' => [
						'type' => 'number',
					],
				],
			],
		];
	}
}

