<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

		$regularList = &$parser->list;
		$parser->list = new WikiPlugin_expandingoutline_list($parser->list);
		$id = $this->id($index);

		$headerlib->add_jq_onready(
<<<JQ
			var base = $('#$id');

			var labels = base.find('td.tikiListTableLabel');

			labels
				.toggle(function(e) {
				    if (e.shiftKey) {
						labels.show();
						return;
				    }

					var child = $('.parentTrail' + $(this).data('trail'));

					if (child.stop().fadeIn().length) {
						$(this).find('img.listImg').attr('src', 'img/toggle-collapse-dark.png');

					}
				}, function(e) {
					if (e.shiftKey) {
						labels.hide();
						return;
				    }

					var child = $('.parentTrail' + $(this).data('trail'));

					if (child.stop().fadeOut().length) {
						$(this).find('img.listImg').attr('src', 'img/toggle-expand-dark.png');
					}
				});

				base.find('td.tikiListTableLabel').prepend('<img class="listImg" src="img/toggle-expand-dark.png" />');
JQ
);


		$headerlib->add_css(
			"#$id table {
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

			.tikiListTableChild {
				display: none;
			}

			.wikiplugin_expandingoutline .tier0 {
				background-color: rgb(255,37,6) ! important;
			}
			.wikiplugin_expandingoutline .tier1 {
				background-color: rgb(254,143,17) ! important;
			}
			.wikiplugin_expandingoutline .tier2 {
				background-color: rgb(249,245,41) ! important;
			}
			.wikiplugin_expandingoutline .tier3 {
				background-color: rgb(111,244,81) ! important;
			}
			.wikiplugin_expandingoutline .tier4 {
				background-color: rgb(83,252,243) ! important;
			}
			.wikiplugin_expandingoutline .tier5 {
				background-color: rgb(138,158,251) ! important;
			}
			.wikiplugin_expandingoutline .tier6 {
				background-color: rgb(206,127,250) ! important;
			}
			.wikiplugin_expandingoutline .tier7 {
				background-color: rgb(250,167,251) ! important;
			}
			.wikiplugin_expandingoutline .tier8 {
				background-color: rgb(255,214,188) ! important;
			}
			.wikiplugin_expandingoutline .tier9 {
				background-color: rgb(255,214,51) ! important;
			}"
		);

		$result = $parser->parse($data);

		$parser->list = $regularList;

		return $result;
	}
}
