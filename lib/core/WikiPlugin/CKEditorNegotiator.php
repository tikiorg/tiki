<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_CKEditorNegotiator extends WikiPlugin_ParserNegotiator
{
	var $needsParsed = false;

	public function execute()
	{
		$plugin_result = parent::execute();
		$syntax = $this->toSyntax();

		// work out if I'm a nested plugin and return empty if so
		$stack = debug_backtrace();
		$plugin_nest_level = 0;
		foreach ($stack as $st) {
			if ($st['function'] === 'execute') {
				$plugin_nest_level ++;
				if ($plugin_nest_level > 1) {
					return '';
				}
			}
		}


		$icon = isset($this->info['icon']) ? $this->info['icon'] : 'img/icons/wiki_plugin_edit.png';

		// some plugins are just too flakey to do wysiwyg, so show the "source" for them ;(
		if (in_array($this->name, array('trackerlist', 'kaltura', 'toc', 'freetagged', 'draw', 'googlemap'))) {
			$plugin_result = '&nbsp;&nbsp;&nbsp;&nbsp;' . $syntax;
		} else {
			// Tiki 7+ adds ~np~ to plugin output so remove them
			$plugin_result = preg_replace('/~[\/]?np~/ms', '', $plugin_result);

			$parser = new JisonParser_Wiki_Handler();
			$parser->setOption(array(
				'is_html' => false,
				'suppress_icons' => true,
				'ck_editor' => true,
				'noparseplugins' => true
			));
			$plugin_result = $parser->parse($plugin_result);

			// remove hrefs and onclicks
			$plugin_result = preg_replace('/\shref\=/i', ' tiki_href=', $plugin_result);
			$plugin_result = preg_replace('/\sonclick\=/i', ' tiki_onclick=', $plugin_result);
			$plugin_result = preg_replace('/<script.*?<\/script>/mi', '', $plugin_result);
		}

		if (!in_array($this->name, array('html'))) {		// remove <p> and <br>s from non-html
			$this->body = str_replace(array('<p>', '</p>', "\t"), '', $this->body);
			$this->body = str_replace('<br />', "\n", $this->body);
		}

		if ($this->containsHtmlBlock($plugin_result)) {
			$elem = 'div';
		} else {
			$elem = 'span';
		}

		$elem_style = 'position:relative;';
		if (in_array($this->name, array('img', 'div')) && preg_match('/<'.$this->name.'[^>]*style="(.*?)"/i', $plugin_result, $m)) {
			if (count($m)) {
				$elem_style .= $m[1];
			}
		}

		$ret = '~np~<'.$elem.' class="tiki_plugin" plugin="' . $this->name . '" style="' . $elem_style . '"' .
			' syntax="' . htmlentities( $syntax, ENT_QUOTES, 'UTF-8') . '"' .
			' args="' . htmlentities($this->urlEncodeArgs(), ENT_QUOTES, 'UTF-8') . '"' .
			' body="' . htmlentities($this->body, ENT_QUOTES, 'UTF-8') . '">'.	// not <!--{cke_protected}
			'<img src="'.$icon.'" width="16" height="16" style="float:left;position:relative;z-index:10001" />' .
			$plugin_result.'<!-- end tiki_plugin --></'.$elem.'>~/np~';

		return 	$ret;
	}

	function containsHtmlBlock($inHtml)
	{
		// detect all block elements as defined on http://www.w3.org/2007/07/xhtml-basic-ref.html
		$block_detect_regexp = '/<[\/]?(?:address|blockquote|div|dl|fieldset|h\d|hr|li|noscript|ol|p|pre|table|ul)/i';
		return  (preg_match($block_detect_regexp, $inHtml) > 0);
	}
}