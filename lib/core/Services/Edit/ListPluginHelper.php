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
			'display' => [
				'params' => [
					'name' => [
						'type' => 'field',
					],
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
									// TODO some way of adding the plugin's params too
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
			'filter' => [
				'params' => [
					'categories' => [
						'type' => 'categories',
					],
					'content' => [
						'type' => 'text',
						'params' => [
							'field' => [
								'type' => 'field',
							],
						],
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
					'editable' => [
						'type' => 'text',
						'params' => [
							'field' => [
								'type' => 'field',
								'required' => true,
							],
						],
					],
					'exact' => [
						'type' => 'text',
						'params' => [
							'field' => [
								'type' => 'field',
								'required' => true,
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
							'editable' => [
								'type' => 'text',
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
			'format' => [
				'params' => [
					'name' => [
						'type' => 'text',
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
			'pagination' => [
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
				'params' => [
					'template' => [
						'options' => [
							'input' => [
								'type' => 'text',
							],
							'table' => [
								'plugins' => [
									'column' => [
										'params' => [
											'sort' => [
												'type' => 'field',
											],
											'label' => [
												'type' => 'text',
											],
											'field' => [
												'type' => 'field',
											],
											'mode' => [
												'options' => [
													'',
													'raw',
												],
											],
										],
									],
									'tablesorter' => [
										'params' => [
											'server' => [
												'type' => 'checkbox',
											],
											'sortable' => [
												'type' => 'text',
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
							'medialist' => [
								'plugins' => [
									'icon' => [
										'params' => [
											'field' => [
												'type' => 'field',
											],
										],
									],
									'body' => [
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
											'id' => [
												'type' => 'text',
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
			'sort' => [
				'params' => [
					'mode' => [
						'type' => 'text',
					],
				],
			],
			'group' => [    // what is this?
				'params' => [
					'boost' => [
						'type' => 'number',
					],
				],
			],
			'wiki text' => [],    // allow wiki text within output and format plugins
		];
	}
}

