<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wysiwyglib.php 44222 2012-12-11 10:09:27Z changi67 $

/*
 * Shared functions for tiki implementation of ckeditor (v3.6.2)
 */

class WYSIWYGLib
{
	function setUpEditor($is_html, $dom_id, $params = array(), $auto_save_referrer = '', $full_page = true)
	{
		global $tikiroot, $headerlib;
		$headerlib->jsfiles[-1][5] = 'lib/aloha-editor/lib/vendor/jquery-ui-1.9m6.js';
		$headerlib
			->add_cssfile('lib/aloha-editor/css/aloha.css')
			->add_jsfile('lib/aloha-editor_tiki/aloha-config.js')
			->add_jsfile('lib/aloha-editor/lib/aloha-core.js')
			//->add_jsfile('lib/aloha-editor/lib/require.js')
			//->add_jsfile('lib/aloha-editor/lib/aloha.js')
			->add_jsfile('lib/aloha-editor/plugins/common/ui/ui.js')
			->add_jsfile('lib/aloha-editor/plugins/common/format/format.js')
			->add_jsfile('lib/aloha-editor/plugins/common/table/table.js')
			->add_jsfile('lib/aloha-editor/plugins/common/list/list.js')
			->add_jsfile('lib/aloha-editor/plugins/common/link/link.js')
			->add_jsfile('lib/aloha-editor/plugins/common/highlighteditables/highlighteditables.js')
			->add_jsfile('lib/aloha-editor/plugins/common/contenthandler/contenthandler.js')
			->add_jsfile('lib/aloha-editor/plugins/common/paste/paste.js')
			->add_jsfile('lib/aloha-editor/plugins/common/characterpicker/characterpicker.js')
			->add_jsfile('lib/aloha-editor/plugins/common/commands/commands.js')
			//->add_jsfile('lib/aloha-editor/plugins/common/block/block.js') //causes too much to be uneditable
			->add_jsfile('lib/aloha-editor/plugins/common/image/image.js')
			//->add_jsfile('lib/aloha-editor/plugins/common/undo/undo.js')
			->add_jsfile('lib/aloha-editor/plugins/common/abbr/abbr.js')
			->add_jsfile('lib/aloha-editor/plugins/common/horizontalruler/horizontalruler.js')
			->add_jsfile('lib/aloha-editor/plugins/common/align/align.js')
			->add_jsfile('lib/aloha-editor/plugins/common/dom-to-xhtml/dom-to-xhtml.js')
			->add_jsfile('lib/aloha-editor_tiki/tiki_aloha-editor.js')
			->add_jsfile('lib/aloha-editor_tiki/jison_parser_wiki.js')
			//->add_jsfile('lib/aloha-editor_tiki/ribbon.js')
			->add_js(
			"
			//$.modal(tr('Loading...'));
			jisonParserWiki.syntax = " . json_encode(JisonParser_WikiCKEditor_Handler::$typeShorthand) . ";
			Aloha.ready(function() {
				Aloha.bind( 'aloha-add-markup', function( jEvent, markup, tracking ) {
					if (!tracking) return;
					var helper = jisonParserWiki.alohaTagHelper[tracking.type];
					if (tracking.namespace == 'aloha-format' && helper) {
						var type = jisonParserWiki.syntax[helper.type];
						if (type) {
							markup.attr('data-t', type);
						}
					}
				});
				$('#$dom_id').aloha();
				//$.modal();
			});
			");

		return '<script>
			window.ALOHA_BASE_URL = "lib/aloha-editor/";
			/*var Aloha = {
				settings: {
					bundles: {
						tiki: "../../aloha-editor_tiki/plugins"
				}
			}
		};*/
		</script>';
	}
}

global $wysiwyglib;
$wysiwyglib = new WYSIWYGLib();

