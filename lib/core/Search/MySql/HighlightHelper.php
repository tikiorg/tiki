<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_HighlightHelper implements Zend_Filter_Interface
{
	private $words = array();
	private $replacements = array();
	private $snippetHelper;

	function __construct(Search_Expr_Interface $query)
	{
		$words = array();
		$factory = new Search_Type_Factory_Direct;
		$query->walk(function ($node) use (& $words, $factory) {
			if ($node instanceof Search_Expr_Token) {
				$word = $node->getValue($factory)->getValue();
				if (is_string($word)) {
					$words[] = $word;
				}
			}
		});

		$this->words = $words;
		$this->replacements = array_map(function ($word) {
			return "<b style=\"color:black;background-color:#ff66ff\">$word</b>";
		}, $this->words);
		$this->snippetHelper = new Search_ResultSet_SnippetHelper;
	}

	function filter($content)
	{
		$content = $this->snippetHelper->filter($content);
		$content = str_ireplace($this->words, $this->replacements, $content);
		return trim(strip_tags($content, '<b><i><em><strong><pre><code><span>'));
	}
}

