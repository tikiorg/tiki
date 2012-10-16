<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_Negotiator_CKEditor extends WikiPlugin_Negotiator_Wiki
{
	public $needsParsed = false;
	public $dontModify = false;

	public function execute()
	{
		$result = parent::execute();

		//This gives us access to the plugin levels
		if ($this->parserLevel > WikiPlugin_ParserNegotiator::$currentParserLevel) return $result;

		$syntax = $this->toSyntax();

		$icon = isset($this->info['icon']) ? $this->info['icon'] : 'img/icons/wiki_plugin_edit.png';

		// some plugins are just too flakey to do wysiwyg, so show the "source" for them ;(
		if (in_array($this->name, array('trackerlist', 'kaltura', 'toc', 'freetagged', 'draw', 'googlemap'))) {
			$result = '&nbsp;&nbsp;&nbsp;&nbsp;' . $syntax;
		} else {
			// Tiki 7+ adds ~np~ to plugin output so remove them
			$result = preg_replace('/~[\/]?np~/ms', '', $result);

			// remove hrefs and onclicks
			$result = preg_replace('/\shref\=/i', ' tiki_href=', $result);
			$result = preg_replace('/\sonclick\=/i', ' tiki_onclick=', $result);
			$result = preg_replace('/<script.*?<\/script>/mi', '', $result);
		}

		if (!in_array($this->name, array('html'))) {		// remove <p> and <br>s from non-html
			$this->body = str_replace(array('<p>', '</p>', "\t"), '', $this->body);
			$this->body = str_replace('<br />', "\n", $this->body);
		}

		if ($this->containsHtmlBlock($result)) {
			$elem = 'div';
		} else {
			$elem = 'span';
		}

		$elem_style = 'position:relative;';
		if (in_array($this->name, array('img', 'div')) && preg_match('/<'.$this->name.'[^>]*style="(.*?)"/i', $result, $m)) {
			if (count($m)) {
				$elem_style .= $m[1];
			}
		}

		return '<'.$elem.' class="tiki_plugin" plugin="' . $this->name . '" style="' . $elem_style . '"' .
			' syntax="' . htmlentities($syntax, ENT_QUOTES, 'UTF-8') . '"' .
			' args="' . htmlentities($this->urlEncodeArgs(), ENT_QUOTES, 'UTF-8') . '"' .
			' body="' . htmlentities($this->body, ENT_QUOTES, 'UTF-8') . '">'.	// not <!--{cke_protected}
			'<img src="'.$icon.'" width="16" height="16" style="float:left;position:absolute;z-index:10001" />' .
			$result.'<!-- end tiki_plugin --></'.$elem.'>';
	}

	function blockFromExecution($status = '')
	{
		return $this->execute();
	}

	private function containsHtmlBlock(& $string)
	{
		// detect all block elements as defined on http://www.w3.org/2007/07/xhtml-basic-ref.html
		$block_detect_regexp = '/<[\/]?(?:address|blockquote|div|dl|fieldset|h\d|hr|li|noscript|ol|p|pre|table|ul)/i';
		return  (preg_match($block_detect_regexp, $string) > 0);
	}
}
