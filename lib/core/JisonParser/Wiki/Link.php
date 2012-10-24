<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Link.php 43612 2012-10-23 14:07:37Z robertplummer $

class JisonParser_Wiki_Link
{
	public $page;
	public $externalLocation;
	public $externalWikiName;
	public $pageLink;
	public $type;
	public $namespace;
	public $namespaceSeparator;
	public $description;
	public $reltype;
	public $processPlural = false;
	public $anchor;
	public $language;
	public $suppressIcons;
	public $skipPageCache = false;

	static $externals = false;

	function __construct()
	{
		global $prefs;

		$this->namespaceSeparator = $prefs['namespace_separator'];
	}

	static function page($page)
	{
		$link = new self();

		$link->page = $page;

		$link->setDescription($page);

		// HTML entities encoding breaks page lookup
		$link->pageLink = html_entity_decode($page, ENT_COMPAT, 'UTF-8');

		if (!empty($namespace)) {
			$link->namespace = $namespace;
		}

		return $link;
	}

	public function setExternalWikiName($externalWikiName)
	{
		$this->externalWikiName = $externalWikiName;

		return $this;
	}

	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}


	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	public function setDescription($description)
	{
		global $prefs;

		if (!empty($description)) {
			$feature_wikiwords = $prefs['feature_wikiwords'];
			$prefs['feature_wikiwords'] = 'n';

			$this->description = $description;

			$prefs['feature_wikiwords'] = $feature_wikiwords;
		}

		return $this;
	}

	public function processPlural($processPlural)
	{
		$this->processPlural = $processPlural;

		return $this;
	}

	public function setSuppressIcons($suppressIcons)
	{
		$this->suppressIcons = $suppressIcons;

		return $this;
	}

	public function setSkipPageCache($skipPageCache)
	{
		$this->skipPageCache = $skipPageCache;

		return $this;
	}

	function externalWikiHtml()
	{
		return '<a href="' . $this->externalWikiUrl() . '">' . (isset($this->description) ? $this->description : $this->page) . '</a>';
	}

	public function externalWikiUrl()
	{
		global $tikilib;

		$url = $tikilib->getOne('SELECT extwiki FROM tiki_extwiki WHERE name = ?', array(strtolower($this->externalWikiName)));

		$url = str_replace('$page', $this->page, $url);

		if (strstr('://', $url) === false) {
			$url = 'http://' . $url;
		}

		return $url;
	}

	function externalHtml()
	{
		global $tikilib, $prefs, $smarty;

		$target = '';
		$class = 'wiki';
		$ext_icon = '';
		$rel = '';
		$cached = '';

		if ($prefs['popupLinks'] == 'y') {
			$target = '_blank"';
		}

		if (!strstr($this->page, '://')) {
			$target = '';
		} else {
			$class .= ' external';
			if ($prefs['feature_wiki_ext_icon'] == 'y' && !$this->suppressIcons) {
				include_once('lib/smarty_tiki/function.icon.php');
				$ext_icon = smarty_function_icon(array('_id'=>'external_link', 'alt'=>tra('(external link)'), '_class' => 'externallink', '_extension' => 'gif', '_defaultdir' => 'img/icons', 'width' => 15, 'height' => 14), $smarty);
			}
			$rel='external';
			if ($prefs['feature_wiki_ext_rel_nofollow'] == 'y') {
				$rel .= ' nofollow';
			}
		}

		if ($prefs['cachepages'] == 'y' && $tikilib->is_cached($this->page)) {
			$cached = " <a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=".urlencode($this->pageLink)."\">(cache)</a>";
		}

		return '<a class="' . $class . '"' .
			(!empty($target) ? ' target="' . $target . '"' : '') .
			' href="' . $this->page .
			(!empty($rel) ? '" rel="' . $rel : '') . '">' . $this->description . '</a>' . $ext_icon . $cached;
	}

	function getHtml()
	{
		switch ($this->type) {
			case 'np':
				return $this->page;
				break;
			case 'alias':
				$this->reltype = $this->type;
				break;
			case 'word':
				break;
			case 'external':
				return $this->externalHtml();
				break;
			case 'externalWiki':
				return $this->externalWikiHtml();
				break;
		}

		global $tikilib, $prefs;
		$wikilib = TikiLib::lib('wiki');

		// Fetch all externals once
		if ( false === self::$externals ) {
			self::$externals = $tikilib->fetchMap('SELECT LOWER(`name`), `extwiki` FROM `tiki_extwiki`');
		}

		$link = new WikiParser_OutputLink;
		$link->setIdentifier($this->pageLink);
		$link->setNamespace($this->namespace, $prefs['namespace_separator']);
		$link->setQualifier($this->reltype);
		$link->setDescription($this->description);
		$link->setWikiLookup(array( &$this, 'findWikiPage' ));
		$link->setWikiLinkBuilder(
			function ($pageName)
			{
				$wikilib = TikiLib::lib('wiki');
				return $wikilib->sefurl($pageName);
			}
		);
		$link->setExternals(self::$externals);
		$link->setHandlePlurals($this->processPlural);
		$link->setAnchor($this->anchor);

		if ( $prefs['feature_multilingual'] == 'y' && isset( $GLOBALS['pageLang'] ) ) {
			$link->setLanguage($GLOBALS['pageLang']);
		}

		return $link->getHtml();
	}

	function findWikiPage()
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');
		$page_info = $tikilib->get_page_info($this->page, false);

		if ( $page_info !== false ) {
			return $page_info;
		}

		// If page does not exist directly, attempt to find an alias
		if ( $prefs['feature_wiki_pagealias'] == 'y' ) {
			$semanticlib = TikiLib::lib('semantic');

			$toPage = $this->page;
			$tokens = explode(',', $prefs['wiki_pagealias_tokens']);

			$prefixes = explode(',', $prefs["wiki_prefixalias_tokens"]);
			foreach ($prefixes as $p) {
				$p = trim($p);
				if (strlen($p) > 0 && TikiLib::strtolower(substr($this->page, 0, strlen($p))) == TikiLib::strtolower($p)) {
					$toPage = $p;
					$tokens = 'prefixalias';
				}
			}

			$links = $semanticlib->getLinksUsing(
				$tokens,
				array( 'toPage' => $toPage )
			);

			if ( count($links) > 1 ) {
				// There are multiple aliases for this page. Need to disambiguate.
				//
				// When feature_likePages is set, trying to display the alias itself will
				// display an error page with the list of aliased pages in the "like pages" section.
				// This allows the user to pick the appropriate alias.
				// So, leave the $pageName to the alias.
				//
				// If feature_likePages is not set, then the user will only see that the page does not
				// exist. So it's better to just pick the first one.
				//
				if ($prefs['feature_likePages'] == 'y' || $tokens == 'prefixalias') {
					// Even if there is more then one match, if prefix is being redirected then better
					// to fail than to show possibly wrong page
					return true;
				} else {
					// If feature_likePages is NOT set, then trying to display the first one is fine
					// $pageName is by ref so it does get replaced
					$pageName = $links[0]['fromPage'];
					return $tikilib->get_page_info($this->page, true);
				}
			} elseif (count($links)) {
				// there is exactly one match
				if ($prefs['feature_wiki_1like_redirection'] == 'y') {
					return true;
				} else {
					$this->page = $links[0]['fromPage'];
					return $tikilib->get_page_info($this->page, true);
				}
			}
		}
	}
}