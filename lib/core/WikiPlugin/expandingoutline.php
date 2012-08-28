<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $$

class WikiPlugin_expandingoutline extends WikiPlugin_HtmlBase
{
	public $type = 'expandingoutline';
	public $documentation = 'PluginExpandingOutline';
	public $prefs = array('wikiplugin_expandingoutline', 'feature_jison_wiki_parser');
	public $filter = 'rawhtml_unsafe';
	public $icon = 'img/icons/mime/html.png';
	public $tags = array( 'basic' );

	function __construct()
	{
		$this->name = tr('Expanding Outline');
		$this->description = tr('Turns wiki list into an expandable outline');
		$this->body = tr('Wiki syntax of list');
		$this->params = array();
	}

	function output(&$data, &$params, &$index, &$parser)
	{
		global $headerlib;

		if (get_class($parser) != 'JisonParser_Wiki_Handler') return TikiLib::lib('tiki')->parse_data($data, array('is_html' => true));

		$regularList = &$parser->list;
		$parser->list = new WikiPlugin_expandingoutline_list($parser->list);
		$id = $this->id($index);

		$headerlib->add_jq_onready(<<<JQ
			var color = [
				'rgb(255,37,6)',
				'rgb(254,143,17)',
				'rgb(249,245,41)',
				'rgb(111,244,81)',
				'rgb(83,252,243)',
				'rgb(138,158,251)',
				'rgb(206,127,250)',
				'rgb(250,167,251)',
				'rgb(255,214,188)',
				'rgb(255,214,51)'
			];
        
			var base = $('#$id');
			base.find('table.tikiListTable:first table')
				.parent().hide();

			var tables = $('#$id').find('table.tikiListTable').each(function() {
				var tier = $(this).data('tier');
				$(this).find('td.tikiListTableLabel,td.tikiListTableBlank').each(function() {
					$(this)
						.css('background-color', color[tier]);
				});
			});

			base.find('td.tikiListTableLabel')
				.toggle(function(e) {
				    if (e.shiftKey) {
						tables.show();
						return;
				    }

					var me = $(this).parent();
					console.log(me.next().children('td'));
					if (me.next().children('td').stop().fadeIn().length) {
						me.find('img.listImg').attr('src', 'img/toggle-expand-dark.png');
					}
				}, function(e) {
					if (e.shiftKey) {
						tables.hide();
						return;
				    }

					var me = $(this).parent();
					if (me.next().children('td').stop().fadeOut().length) {
						me.find('img.listImg').attr('src', 'img/toggle-collapse-dark.png');
					}
				})
				.each(function() {
					if ($(this).parent().next().html().match('table'))
						$(this).prepend('<img class="listImg" src="img/toggle-collapse-dark.png" />');
				});
JQ
);

		$result = "<style>
			#$id table {
				width: 100%;
				border-collapse:collapse;
			}
			#$id * {
				border-width: 0px;
			}
			#$id .tikiListTable td, #$id .tikiListTable {
				font-size: 14px;
				background-color: white;
				list-style-type: none;
			}
			#$id .tikiListTableLabel
			{
				width: 1px;
				white-space: nowrap;

			}
		</style>" . $parser->parsePlugin($data);

		$parser->list = $regularList;

		return $result;
	}
}