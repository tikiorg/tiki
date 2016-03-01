<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_HighlightHelper implements Zend\Filter\FilterInterface
{
	private $words = array();
	private $replacements = array();
	private $snippetHelper;

	function __construct(array $words)
	{
		$this->words = $words;
		$this->replacements = array_map(
			function ($word) {
				return "<b style=\"color: rgb(60, 118, 61);background: rgb(223, 240, 216);\">$word</b>";
			}, $this->words
		);
		$this->snippetHelper = new Search_ResultSet_SnippetHelper;
	}

	function filter($content)
	{
		$content = $this->snippetHelper->filter($content);
		$content = str_ireplace($this->words, $this->replacements, $content);
		return trim(strip_tags($content, '<b><i><em><strong><pre><code><span>'));
	}
}

