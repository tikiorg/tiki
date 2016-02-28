<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/****
 * Initially just a collection of the functions dotted around tiki-editpage.php for v4.0
 * Started in the edit_fixup experimental branch - jonnybradley Aug 2009
 *
 */

class EditLib
{
	private $tracesOn = false;

	// Fields for translation related methods.
	public $sourcePageName = null;
	public $targetPageName = null;
	public $oldSourceVersion = null;
	public $newSourceVersion = null;

	// Fields for handling links to external wiki pages
	private $external_wikis = null;


	// general

	function make_sure_page_to_be_created_is_not_an_alias($page, $page_info)
	{
		$access = TikiLib::lib('access');
		$tikilib = TikiLib::lib('tiki');
		$wikilib = TikiLib::lib('wiki');
		$semanticlib = TikiLib::lib('semantic');

		$aliases = $semanticlib->getAliasContaining($page, true);
		if (!$page_info && count($aliases) > 0) {
			$error_title = tra("Cannot create aliased page");
			$error_msg = tra("You attempted to create the following page:")." ".
			             "<b>$page</b>.\n<p>\n";
			$error_msg .= tra("That page is an alias for the following pages").": ";
			foreach ($aliases as $an_alias) {
				$error_msg .= '<a href="'.$wikilib->editpage_url($an_alias['fromPage'], false).'">'.$an_alias['fromPage'].'</a>, ';
			}
			$error_msg .= "\n<p>\n";
			$error_msg .= tra("If you want to create the page, you must first edit each of the pages above to remove the alias link they may contain. This link should look something like this");
			$error_msg .= ": <b>(alias($page))</b>";

			$access->display_error(page, $error_title, "", true, $error_msg);
		}
	}

	function user_needs_to_specify_language_of_page_to_be_created($page, $page_info, $new_page_inherited_attributes = null)
	{
		global $prefs;
		if (isset($_REQUEST['need_lang']) && $_REQUEST['need_lang'] == 'n') {
			return false;
		}
		if ($prefs['feature_multilingual'] == 'n') {
			return false;
		}
		if ($page_info && isset($page_info['lang']) && $page_info['lang'] != '') {
			return false;
		}
		if (isset($_REQUEST['lang']) && $_REQUEST['lang'] != '') {
			return false;
		}
		if ($new_page_inherited_attributes != null &&
			isset($new_page_inherited_attributes['lang']) &&
			$new_page_inherited_attributes['lang'] != '') {
			return false;
		}

		return true;
	}

	// translation functions

	function isTranslationMode()
	{
		return $this->isUpdateTranslationMode() || $this->isNewTranslationMode();
	}

	function isNewTranslationMode()
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y') {
			return false;
		}
		if (isset( $_REQUEST['translationOf']  )
			&& ! empty( $_REQUEST['translationOf'] )) {

			return true;
		}
		if (isset( $_REQUEST['is_new_translation']  )
			&& $_REQUEST['is_new_translation'] ==  'y') {
			return true;
		}
		return false;
	}

	function isUpdateTranslationMode()
	{
		return isset( $_REQUEST['source_page'] )
			&& isset( $_REQUEST['oldver'] )
			&& (!isset($_REQUEST['is_new_translation']) || $_REQUEST['is_new_translation'] == 'n')
			&& isset( $_REQUEST['newver'] );
	}

	function prepareTranslationData()
	{
		$this->setTranslationSourceAndTargetPageNames();
		$this->setTranslationSourceAndTargetVersions();
	}

	private function setTranslationSourceAndTargetPageNames()
	{
		$smarty = TikiLib::lib('smarty');

		if (!$this->isTranslationMode()) {
			return;
		}

		$this->targetPageName = null;
		if (isset($_REQUEST['target_page'])) {
			$this->targetPageName = $_REQUEST['target_page'];
		} elseif (isset($_REQUEST['page'])) {
			$this->targetPageName = $_REQUEST['page'];
		}
		$smarty->assign('target_page', $this->targetPageName);

		$this->sourcePageName = null;
		if (isset($_REQUEST['translationOf']) && $_REQUEST['translationOf']) {
			$this->sourcePageName = $_REQUEST['translationOf'];
		} elseif (isset($_REQUEST['source_page'])) {
			$this->sourcePageName = $_REQUEST['source_page'];
		}
		$smarty->assign('source_page', $this->sourcePageName);

		if ($this->isNewTranslationMode()) {
			$smarty->assign('translationIsNew', 'y');
		} else {
			$smarty->assign('translationIsNew', 'n');

		}
	}

	private function setTranslationSourceAndTargetVersions()
	{
		global $_REQUEST, $tikilib;

		if (isset($_REQUEST['oldver'])) {
			$this->oldSourceVersion = $_REQUEST['oldver'];
		} else {
			// Note: -1 means a "virtual" empty version.
			$this->oldsourceVersion = -1;
		}

		if (isset($_REQUEST['newver'])) {
			$this->newSourceVersion = $_REQUEST['newver'];
		} else {
			// Note: version number of 0 means the most recent version.
			$this->newSourceVersion = 0;
		}
	}

	function aTranslationWasSavedAs($complete_or_partial)
	{
		if (!$this->isTranslationMode() ||
			!isset($_REQUEST['save'])) {
			return false;
		}

		// We are saving a translation. Is it partial or complete?
		if ($complete_or_partial == 'complete' && isset($_REQUEST['partial_save'])) {
			return false;
		} else if ($complete_or_partial == 'partial' && !isset($_REQUEST['partial_save'])) {
			return false;
		}
		return true;
	}


	/**
	 * Convert rgb() color definiton to hex color definiton
	 *
	 * @param unknown_type $col
	 * @return The hex representation
	 */
	function parseColor(&$col)
	{

		if (preg_match("/^rgb\( *(\d+) *, *(\d+) *, *(\d+) *\)$/", $col, $parts) ) {
			$hex = str_pad(dechex($parts[1]), 2, '0', STR_PAD_LEFT)
			     . str_pad(dechex($parts[2]), 2, '0', STR_PAD_LEFT)
				 . str_pad(dechex($parts[3]), 2, '0', STR_PAD_LEFT);
			$hex = '#' . TikiLib::strtoupper($hex);
		} else {
			$hex = $col;
		}

		return $hex;
	}


	/**
	 * Utility for walk_and_parse to process links
	 *
	 * @param array $args the attributes of the link
	 * @param array $text the link text
	 * @param string $src output string
	 * @param array $p ['stack'] = closing strings stack
	 */
	private function parseLinkTag(&$args, &$text, &$src, &$p)
	{

		global $prefs;

		$link = '';
		$link_open = '';
		$link_close = '';

		/*
		 * parse the link classes
		 */
		$cl_wiki = false;
		$cl_wiki_page = false; // Wiki page
		$cl_ext_page = false; // external Wiki page
		$cl_external = false; // external web page
		$cl_semantic = ''; // semantic link
		if ($prefs['feature_semantic'] === 'y') {
			$semantic_tokens = TikiLib::lib('semantic')->getAllTokens();
		} else {
			$semantic_tokens = array();
		}

		$ext_wiki_name = '';

		if ( isset($args['class']) && isset($args['href']) ) {
			$matches = array();
			preg_match_all('/([^ ]+)/', $args['class']['value'], $matches);
			$classes = $matches[0];

			for ($i=0, $count_classes = count($classes); $i< $count_classes; $i++) {
				$cl = $classes[$i];

				switch ($cl) {
					case 'wiki': $cl_wiki = true;
						break;
 					case 'wiki_page': $cl_wiki_page = true;
						break;
  					case 'ext_page': $cl_ext_page = true;
						break;
 					case 'external': $cl_external = true;
						break;
					default:
						// if the preceding class was 'ext_page', then we have the name of the external Wiki
						if ($i > 0 && $classes[$i-1] == 'ext_page') {
							$ext_wiki_name = $cl;
						}
						if (in_array($cl, $semantic_tokens)) {
							$cl_semantic = $cl;
						}
				}
			}
		}


		/*
		 * extract the target and the anchor from the href
		 */
		if ( isset($args['href']) ) {
			$href = urldecode($args['href']['value']);
			$matches = explode('#', $href);
			if ( count($matches) == 2) {
				$target = $matches[0];
				$anchor = '#' . $matches[1];
			} else {
				$target = $href;
				$anchor = '';
			}
		} else {
			$target = '';
			$anchor = '';
		}


		/*
		 * treat invalid external Wikis as web links
		 */
		if ( $cl_ext_page ) {

			// retrieve the definitions from the database
			if ($this->external_wikis == null) {
				global $tikilib;
				$query = 'SELECT `name`, `extwiki` FROM `tiki_extwiki`';
				$this->external_wikis = $tikilib->fetchMap($query);
			}

			// name must be set and defined
			if ( !$ext_wiki_name || !isset($this->external_wikis[$ext_wiki_name]) ) {
				$cl_ext_page = false;
				$cl_wiki_page = false;
			}
		};


		/*
		 * construct the links according to the defined classes
		 */
		if ( $cl_wiki_page ) {

			/*
			 * link to wiki page -> (( ))
			 */
			$link_open = "($cl_semantic(";
			$link_close = '))';

			// remove the html part of the target
			$target = preg_replace('/tiki\-index\.php\?page\=/', '', $target);

			// construct the link
			$link = $target;
			if ($anchor) {
				$link .= '|' . $anchor;
			}

		} else if ( $cl_ext_page ) {

			/*
			 * link to external Wiki page ((:))
			 */
			$link_open = '((';
			$link_close = '))';

			// remove the extwiki definition from the target
			$def = preg_replace('/\$page/', '', $this->external_wikis[$ext_wiki_name]);
			$def = preg_quote($def, '/');
			$target = preg_replace('/^' . $def.'/', '', $target);

			// construct the link
			$link = $ext_wiki_name . ':' . $target;
			if ($anchor) {
				$link .= '|' . $anchor;
			}

		} else if ($cl_wiki && !$cl_external && !$target && strlen($anchor) > 0  && substr($anchor, 0, 1) == '#' ) {

			/*
			 * inpage links [#]
			 */
			$link_open = '[';
			$link_close = ']';

			// construct the link
			$link = $target = $anchor;
			$anchor = '';

		} else if ($cl_wiki && !$cl_external) {

			/*
			 * other tiki resources []
			 * -> articles, ...
			 */
			$link_open = '[';
			$link_close = ']';

			// construct the link
			$link = $target;

		} else if (!$cl_wiki && !$cl_external && !$text && isset($args['id']) && isset($args['id']['value']) ) {

			/*
			 * anchor
			 */
			 $link_open = '{ANAME()}';
			 $link_close = '{ANAME}';
			 $link = $args['id']['value'];

		} else {

			/*
			 * other links []
			 */
			$link_open = '[';
			$link_close = ']';


			/*
			 * parse the rel attribute
			 */
			$box = '';

			if ( isset($args['class']) && isset($args['rel']) ) {
				$matches = array();
				preg_match_all('/([^ ]+)/', $args['rel']['value'], $matches);
				$rels = $matches[0];

				for ($i=0, $count_rels = count($rels); $i< $count_rels; $i++) {
					$r = $rels[$i];

					if (preg_match('/^box/', $r) ) {
						$box = $r;
					}
				}
			}

			// construct the link
			$link = $target;
			if ($anchor) {
				$link .= $anchor;
			}
			// the box must be appended to the text
			if ($box) {
				$text .= '|' . $box;
			}

		} // convert links



		/*
		 * flush the constructed link
		 */
		if ($link_open && $link_close) {

			$p['wiki_lbr']++; // force wiki line break mode

			// does the link text match the target?
			if ($target == trim($text)) {
				$text = '';	 // make sure walk_and_parse() does not append any text.
			} else {
				$link .= '|'; // the text will be appended by walk_and_parse()
			}

			// process the tag and update the output
			$this->processWikiTag('a', $src, $p, $link_open, $link_close, true);
			$src .= $link;
		}
	}


	/**
	 * Utility for walk_and_parse to process p and div tags
	 *
	 * @param bool $isPar True if we process a <p>, false if a <div>
	 * @param array $args the attributes of the tag
	 * @param string $src output string
	 * @param array $p ['stack'] = closing strings stack
	 */
	private function parseParDivTag($isPar, &$args, &$src, &$p)
	{

		global $prefs;

		if (isset($args['style']) || isset($args['align'])) {
			$tag_name = $isPar ? 'p' : 'div'; // key for the $p[stack]
			$type = $isPar ? 'type="p", ' : ''; // used for {DIV()}

			$style = array();
			$this->parseStyleAttribute($args['style']['value'], $style);


			/*
		 	* convert 'align' to 'style' definitions
		    */
			if (isset($args['align'])) {
				$style['text-align'] = $args['align']['value'];
			}


			/*
			 * process all the defined styles
			 */
			foreach (array_keys($style) as $format) {
				switch ($format) {
					case 'text-align':
						if ($style[$format] == 'left') {
							$src .= "{DIV(${type}align=\"left\")}";
							$p['stack'][] = array('tag' => $tag_name, 'string' => '{DIV}');
						} elseif ($style[$format] == 'center') {
							$markup = ($prefs['feature_use_three_colon_centertag'] == 'y') ? ':::' : '::';
							$this->processWikiTag($tag_name, $src, $p, $markup, $markup, false);
						} elseif ($style[$format] == 'right') {
							$src .= "{DIV(${type}align=\"right\")}";
							$p['stack'][] = array('tag' => $tag_name, 'string' => '{DIV}');
						} elseif ($style[$format] == 'justify') {
							$src .= "{DIV(${type}align=\"justify\")}";
							$p['stack'][] = array('tag' => $tag_name, 'string' => '{DIV}');
						}
    					break;
				} // switch format
			} // foreach style
		}
	}


	/**
	 * Utility for walk_and_parse to process span arguments
	 *
	 * @param array $args the attributes of the span
	 * @param string $src output string
	 * @param array $p ['stack'] = closing strings stack
	 */
	private function parseSpanTag(&$args, &$src, &$p)
	{

		if (isset($args['style'])) {
			$style = array();
			$this->parseStyleAttribute($args['style']['value'], $style);

			$this->processWikiTag('span', $src, $p, '', '', true); // prepare the stacks, later we will append


			/*
			 * The colors need to be handeled separatly; two style definitions become
			 * one single wiki markup.
			 */
			$fcol = '';
			$bcol = '';

			if (isset($style['color'])) {
				$fcol = $this->parseColor($style['color']);
				unset($style['color']);
			}
			if (isset($style['background-color'])) { // background: color-def have been converted to background-color
				$bcol = $this->parseColor($style['background-color']);
				unset($style['background-color']);
			}

			if ($fcol || $bcol) {
				$col  = "~~";
				$col .= ($fcol ? $fcol : ' ');
				$col .= ($bcol ? ','.$bcol : '');
				$col .= ':';

				$this->processWikiTag('span', $src, $p, $col, '~~', true, true);
			}


			/*
			 * Process the remaining format definitions
			 */
			foreach (array_keys($style) as $format) {
				switch ($format) {
					case 'font-weight':
						if ($style[$format] == 'bold') {
							$this->processWikiTag('span', $src, $p, '__', '__', true, true);
						}
    					break;
					case 'font-style':
						if ($style[$format] == 'italic') {
							$this->processWikiTag('span', $src, $p, '\'\'', '\'\'', true, true);
						}
					case 'text-decoration':
						if ($style[$format] == 'line-through') {
							$this->processWikiTag('span', $src, $p, '--', '--', true, true);
						} else if ($style[$format] == 'underline') {
							$this->processWikiTag('span', $src, $p, '===', '===', true, true);
						}
				} // switch format
			} // foreach style
		} // style
	}


	/**
	 * Parse a html style definition into an array
	 *
	 * This method tries to expand the shorthand definitions, such as 'background:',
	 * to the correspoinding key/value paris. If a definition is unknown, it is kept.
	 *
	 * @param string $style The value of the style attribute
	 * @param array $parsed key/value pairs
	 */
	function parseStyleAttribute(&$style, &$parsed)
	{

		$matches = array();
		preg_match_all('/ *([^ :]+) *: *([^;]+) *;?/', $style, $matches);

		for ($i=0, $count_matches = count($matches[0]); $i<$count_matches; $i++) {
			$key = $matches[1][$i];
			$value = trim($matches[2][$i]);

			/*
			 * shortand list 'background:'
			 * - set 'background-color'
			 */
			if ($key == 'background') {

				$unprocessed = '';
				$shorthand = array();
				$this->parseStyleList($value, $shorthand);

				foreach ($shorthand as $s) {

					switch ($s) {
						case preg_match('/^#(\w{3,6})$/', $s) > 0:
							$parsed['background-color'] = $s;
							break;
						case preg_match('/^rgb\(.*\)$/', $s) > 0:
							$parsed['background-color'] = $s;
							break;
						default:
							$unprocessed .= ' ' . $s;
					}
				} // foreach shorthand

				// keep unprocessed list entries
				$value = trim($unprocessed);

			} // background:

			// save the result
			if ($value) {
				$parsed[$key] = $value;
			}
		} // style definitions
	}


	/**
	 * Parse a space separated list of html styles
	 *
	 * Example: "rgb( 1, 2, 3) url(background.gif)"
	 *
	 * @param string $list List of styles
	 * @param array $parsed The parsed list
	 */
	function parseStyleList(&$list, &$parsed)
	{

		$matches = array();
		preg_match_all('/(?:[[:graph:]]+\([^\)]*\))|(?:[^ ]+)/', $list, $matches);
		$parsed = $matches[0];
	}


	/**
	 * Utility of walk_and_parse to process a wiki tag
	 *
	 * Wiki tags need a special treatment: In html, a tag may contain
	 * several line breaks. In Wiki however, line breaks are often not allowed
	 * and sometimes additional line breaks are required.
	 *
	 * This method saves Wiki tags an line break information in separate stacks.
	 * These stackes are used in walk_and_parse to:
	 * - Output the required markup before and after the linebreaks (<br />).
	 * - To ensure that linebreaks are, if required, inserted at the correct place (\n).
	 *
	 * @param string $tag The name of the html tag
	 * @param string $src Th Output string
	 * @param array  $p   ['stack'] = closing strings stack,
	 * @param string $begin The wiki markup that begins the tag
	 * @param string $end The wiki markup that ends the tag
	 * @param bool $is_inline True if the tag is inline, false if the tag must span exacly one line.
	 * @param bool $append True = append to the topmost element on the stack, false = create a new element on the stack
	 */
	private function processWikiTag($tag, &$src, &$p, $begin, $end, $is_inline, $append = false)
	{

		// append=false, create new entries on the stack
		if (!$append) {
			$p['stack'][] = array('tag' => $tag, 'string' => '', 'wikitags' => 0 );
			$p['wikistack'][] = array( 'begin' => array(), 'end' => array() );
		};

		// get the entry points on the stacks
		$keys = array_keys($p['wikistack']);
		$key = end($keys);
		$wiki = &$p['wikistack'][$key];

		$keys = array_keys($p['stack']);
		$key = end($keys);
		$stack = &$p['stack'][$key];
		$string = &$stack['string'];

		// append to the stacks
		$wiki['begin'][] = $begin;
		$wiki['end'][] = $end;
		$string = $end . $string;
		$stack['wikitags']++;

		// update the output string
		if (!$is_inline) {
			$this->startNewLine($src);
		}
		$src .= $begin;
	}


	function saveCompleteTranslation()
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$tikilib = TikiLib::lib('tiki');

		$sourceInfo = $tikilib->get_page_info($this->sourcePageName);
		$targetInfo = $tikilib->get_page_info($this->targetPageName);

		$multilinguallib->propagateTranslationBits(
			'wiki page',
			$sourceInfo['page_id'],
			$targetInfo['page_id'],
			$sourceInfo['version'],
			$targetInfo['version']
		);
		$multilinguallib->deleteTranslationInProgressFlags($targetInfo['page_id'], $sourceInfo['lang']);
	}

	function savePartialTranslation()
	{
		$tikilib = TikiLib::lib('tiki');
		$multilinguallib = TikiLib::lib('multilingual');

		$sourceInfo = $tikilib->get_page_info($this->sourcePageName);
		$targetInfo = $tikilib->get_page_info($this->targetPageName);

		$multilinguallib->addTranslationInProgressFlags($targetInfo['page_id'], $sourceInfo['lang']);

	}

	/**
	 * Function to take html from ckeditor and parse back to wiki markup
	 * Used by "switch editor" and when saving in wysiwyg_htmltowiki mode
	 * When saving in mixed "html" mode the "unparsing" is done in JavaScript client-side
	 *
	 * @param $inData string	editor content
	 * @return string			wiki markup
	 */

	function parseToWiki( $inData )
	{

		global $prefs;

		$parsed = $this->partialParseWysiwygToWiki($inData);	// remove cke type plugin wrappers

		$parsed = html_entity_decode($parsed, ENT_QUOTES, 'UTF-8');
		$parsed = preg_replace('/\t/', '', $parsed); // remove all tabs inserted by the CKE

		$parsed = preg_replace_callback('/<pre class=["\']tiki_plugin["\']>(.*?)<\/pre>/ims', array($this, 'parseToWikiPlugin'), $parsed);	// rempve plugin wrappers

		$parsed = $this->parse_html($parsed);
		$parsed = preg_replace('/\{img\(? src=.*?img\/smiles\/icon_([\w\-]*?)\..*\}/im', '(:$1:)', $parsed);	// "unfix" smilies
		$parsed = preg_replace('/&nbsp;/m', ' ', $parsed);												// spaces
		$parsed = preg_replace('/!(?:\d\.)+/', '!#', $parsed); // numbered headings
		if ($prefs['feature_use_three_colon_centertag'] == 'y') { // numbered and centerd headings
			$parsed = preg_replace('/!:::(?:\d\.)+ *(.*):::/', '!#:::\1:::', $parsed);
		} else {
			$parsed = preg_replace('/!::(?:\d\.)+ *(.*)::/', '!#::\1::', $parsed);
		}

		// remove empty center tags
		if ($prefs['feature_use_three_colon_centertag'] == 'y') { // numbered and centerd headings
			$parsed = preg_replace('/::: *:::\n/', '', $parsed);
		} else {
			$parsed = preg_replace('/:: *::\n/', '', $parsed);
		}

		// Put back htmlentities as normal char
		$parsed = htmlspecialchars_decode($parsed, ENT_QUOTES);
		return $parsed;
	}

	function parseToWikiPlugin($matches)
	{
		if (count($matches) > 1) {
			return nl2br($matches[1]);
		}
	}

	/**
	 * Render html to send to ckeditor, including parsing plugins for wysiwyg editing
	 * From both wiki page source (for wysiwyg_htmltowiki) and "html" modes
	 *
	 * @param $inData string	page data, can be wiki or mixed html/wiki
	 * @param bool $fromWiki	set if converting from wiki page using "switch editor"
	 * @param bool $isHtml 		set if are doing WYSIWYG Wiki
	 * @return string			html to send to ckeditor
	 */

	function parseToWysiwyg( $inData, $fromWiki = false, $isHtml = false, $options = array() )
	{
		global $tikilib, $tikiroot, $prefs;
		// Parsing page data for wysiwyg editor
		$inData = $this->partialParseWysiwygToWiki($inData);	// remove any wysiwyg plugins so they don't get double parsed
		$parsed = preg_replace('/(!!*)[\+\-]/m', '$1', $inData);		// remove show/hide headings
		$parsed = preg_replace('/&#039;/', '\'', $parsed);			// catch single quotes at html entities

		$parsed = $tikilib->parse_data(
			$parsed,
			array_merge( array(
				'absolute_links'=>true,
				'noheaderinc'=>true,
				'suppress_icons' => true,
				'ck_editor' => true,
				'is_html' => ($isHtml && !$fromWiki),
				'process_wiki_paragraphs' => (!$isHtml || $fromWiki),
				'process_double_brackets' => 'n'
			), $options )
		);

		if ($fromWiki) {
			$parsed = preg_replace('/^\s*<p>&nbsp;[\s]*<\/p>\s*/iu', '', $parsed);						// remove added empty <p>
		}
		$parsed = preg_replace('/<span class=\"img\">(.*?)<\/span>/im', '$1', $parsed);					// remove spans round img's
		// Workaround Wysiwyg Image Plugin Editor in IE7 erases image on insert http://dev.tiki.org/item3615
		$parsed2 = preg_replace('/(<span class=\"tiki_plugin\".*?plugin=\"img\".*?><\/span>)<\/p>/is', '$1<span>&nbsp;</span></p>', $parsed);
		if ($parsed2 !== null) {
			$parsed = $parsed2;
		}
		// Fix IE7 wysiwyg editor always adding absolute path
		$search = '/(<a[^>]+href=\")https?\:\/\/' . preg_quote($_SERVER['HTTP_HOST'].$tikiroot, '/') . '([^>]+_cke_saved_href)/i';
		$parsed = preg_replace($search, '$1$2', $parsed);

		if (!$isHtml) {
			// Fix for plugin being the last item in a page making it impossible to add new lines (new text ends up inside the plugin)
			$parsed = preg_replace('/<!-- end tiki_plugin --><\/(span|div)>(<\/p>)?$/', '<!-- end tiki_plugin --></$1>&nbsp;$2', $parsed);
			// also if first
			$parsed = preg_replace('/^<(div|span) class="tiki_plugin"/', '&nbsp;<$1 class="tiki_plugin"', $parsed);
		}

		return $parsed;
	}

	/**
	 * Converts wysiwyg plugins into wiki.
	 * Also processes headings by removing surrounding <p> (possibly for wysiwyg_wiki_semi_parsed but not tested)
	 * Also used by ajax preview in Services_Edit_Controller
	 *
	 * @param string $inData	page data - mostly html but can have a bit of wiki in it
	 * @return string			html with wiki plugins
	 */

	function partialParseWysiwygToWiki( $inData )
	{

		// de-protect ck_protected comments
		$ret = preg_replace('/<!--{cke_protected}{C}%3C!%2D%2D%20end%20tiki_plugin%20%2D%2D%3E-->/i', '<!-- end tiki_plugin -->', $inData);
		// remove the wysiwyg plugin elements leaving the syntax only remaining
		$ret = preg_replace('/<(?:div|span)[^>]*syntax="(.*)".*end tiki_plugin --><\/(?:div|span)>/Umis', "$1", $ret);
		// preg_replace blows up here with a PREG_BACKTRACK_LIMIT_ERROR on pages with "corrupted" plugins
		if (!$ret) {
			$ret = $inData;
		}

		// take away the <p> that f/ck introduces around wiki heading ! to have maketoc/edit section working
		$ret = preg_replace('/<p>!(.*)<\/p>/iu', "!$1\n", $ret);

		// strip the last empty <p> tag generated somewhere (ckeditor 3.6, Tiki 10)
		$ret = preg_replace('/\s*<p>[\s]*<\/p>\s*$/iu', "$1\n", $ret);
		return $ret;
	}

	// parse HTML functions

	/**
	 * \brief Parsed HTML tree walker (used by HTML sucker)
	 *
	 * This is initial implementation (stupid... w/o any intellegence (almost :))
	 * It is rapidly designed version... just for test: 'can this feature be useful'.
	 * Later it should be replaced by well designed one :) don't bash me now :)
	 *
	 * \param &$c array -- parsed HTML
	 * \param &$src string -- output string
	 * \param &$p array -- ['stack'] = closing strings stack,
	                       ['listack'] = stack of list types currently opened
	                       ['first_td'] = flag: 'is <tr> was just before this <td>'
	                       ['first_tr'] = flag: 'is <table> was just before this <tr>'
	 */
	function walk_and_parse(&$c, &$src, &$p, $head_url )
	{
		global $prefs;
		// If no string
		if (!$c) {
			return;
		}

		for ($i=0; $i <= $c["contentpos"]; $i++) {
			// If content type 'text' output it to destination...
			if ($c[$i]["type"] == "text") {
				if ( ! ctype_space($c[$i]["data"]) ) {
					$add = $c[$i]["data"];
					$noparsed = array();
					 TikiLib::lib('parser')->plugins_remove($add, $noparsed);
					$add = str_replace(array("\r","\n"), '', $add);
					$add = str_replace('&nbsp;', ' ', $add);
					TikiLib::lib('parser')->plugins_replace($add, $noparsed, true);
					$src .= $add;
				} else {
					$src .= str_replace(array("\n", "\r"), '', $c[$i]["data"]);	// keep the spaces
				}
			} elseif ($c[$i]["type"] == "comment") {
				$src .= preg_replace('/<!--/', "\n~hc~", preg_replace('/-->/', "~/hc~\n", $c[$i]["data"]));
			} elseif ($c[$i]["type"] == "tag") {
				if ($c[$i]["data"]["type"] == "open") {
					// Open tag type

					// deal with plugins - could be either span of div so process before the switch statement
					if (isset($c[$i]['pars']['plugin']) && isset($c[$i]['pars']['syntax'])) {	// handling for tiki plugins
						$src .= html_entity_decode($c[$i]['pars']['syntax']['value']);
						$more_spans = 1;
						$elem_type = $c[$i]["data"]["name"];
						$other_elements = 0;
						$j = $i + 1;
						while ($j < $c['contentpos']) {	// loop through contents of this span and discard everything
							if ($c[$j]['data']['name'] == $elem_type && $c[$j]['data']['type'] == 'close') {
								$more_spans--;
								if ($more_spans === 0) {
									break;
								}
//							} else if ($c[$j]['data']['name'] == 'br' && $more_spans === 1 && $other_elements === 0) {
							} else if ($c[$j]['data']['name'] == $elem_type && $c[$j]['data']['type'] == 'open') {
								$more_spans++;
							} else if ($c[$j]['data']['type'] == 'open' && $c[$j]['data']['name'] != 'br' && $c[$j]['data']['name'] != 'img' && $c[$j]['data']['name'] != 'input') {
								$other_elements++;
							} else if ($c[$j]['data']['type'] == 'close') {
								$other_elements--;
							}
							$j++;
						}
						$i = $j;	// skip everything that was inside this span

					}

					$isPar = false; // assuming "div" when calling parseParDivTag()

					switch ($c[$i]["data"]["name"]) {
						// Tags we don't want at all.
						case "meta": $c[$i]["content"] = '';
							break;

						// others we do want
						case "br":
							if ($p['wiki_lbr']) { // "%%%" or "\n" ?
								$src .= ' %%% ';
							} else {
								// close all wiki tags
								foreach ( array_reverse($p['wikistack']) as $wiki_arr ) {
									foreach ( array_reverse($wiki_arr['end']) as $end ) {
										$src .= $end;
									}
								}
								$src .= "\n";

								// for lists, we must prepend '+' to keep the indentation
								if ($p['listack']) {
									$src .= str_repeat('+', count($p['listack']));
								}

								// reopen all wikitags
								foreach ( $p['wikistack'] as $wiki_arr ) {
									foreach ( $wiki_arr['begin'] as $begin) {
										$src .= $begin;
									}
								}
							}
							break;
						case "hr": $src .= $this->startNewLine($src) . '---';
							break;
						case "title": $src .= "\n!"; $p['stack'][] = array('tag' => 'title', 'string' => "\n");
							break;
						case "p":
							$isPar = true;
							if ($src && $prefs['feature_wiki_paragraph_formatting'] !== 'y') {
								$src.="\n";
							}
						case "div": // Wiki parsing creates divs for center
							if (isset($c[$i]['pars'])) {
								$this->parseParDivTag($isPar, $c[$i]['pars'], $src, $p);
							} else {	// normal para or div
								$src .= $this->startNewLine($src);
								$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "\n\n");
							}
							break;
						case "span":
							if ( isset($c[$i]['pars'])) {
								$this->parseSpanTag($c[$i]['pars'], $src, $p);
							}
    						break;
						case "b": $this->processWikiTag('b', $src, $p, '__', '__', true);
    						break;
						case "i": $this->processWikiTag('i', $src, $p, '\'\'', '\'\'', true);
    						break;
						case "em": $this->processWikiTag('em', $src, $p, '\'\'', '\'\'', true);
    						break;
						case "strong": $this->processWikiTag('strong', $src, $p, '__', '__', true);
    						break;
						case "u":  $this->processWikiTag('u', $src, $p, '===', '===', true);
    						break;
						case "strike": $this->processWikiTag('strike', $src, $p, '--', '--', true);
    						break;
						case "del": $this->processWikiTag('del', $src, $p, '--', '--', true);
    						break;
						case "center":
							if ($prefs['feature_use_three_colon_centertag'] == 'y') {
								$src .= ':::';
								$p['stack'][] = array('tag' => 'center', 'string' => ':::');
							} else {
								$src .= '::';
								$p['stack'][] = array('tag' => 'center', 'string' => '::');
							}
    						break;
						case "code": $src .= '-+'; $p['stack'][] = array('tag' => 'code', 'string' => '+-');
    						break;
						case "dd": $src .= ':'; $p['stack'][] = array('tag' => 'dd', 'string' => "\n");
    						break;
						case "dt": $src .= ';'; $p['stack'][] = array('tag' => 'dt', 'string' => '');
    						break;

						case "h1":
						case "h2":
						case "h3":
						case "h4":
						case "h5":
						case "h6":
							$p['wiki_lbr']++; // force wiki line break mode
							$hlevel = (int) $c[$i]["data"]["name"]{1};
							if (isset($c[$i]['pars']['style']['value']) && strpos($c[$i]['pars']['style']['value'], 'text-align: center;') !== false ) {
								if ($prefs['feature_use_three_colon_centertag'] == 'y') {
									$src .= $this->startNewLine($src) . str_repeat('!', $hlevel) . ':::';
									$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => ":::\n");
								} else {
									$src .= $this->startNewLine($src) . str_repeat('!', $hlevel) . '::';
									$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "::\n");
								}
							} else {	// normal para or div
								$src .= $this->startNewLine($src) . str_repeat('!', $hlevel);
								$p['stack'][] = array('tag' => $c[$i]["data"]["name"], 'string' => "\n");
							}
    						break;
						case "pre": $src .= "~pre~\n"; $p['stack'][] = array('tag' => 'pre', 'string' => "~/pre~\n");
    						break;
						case "sub": $src .= "{SUB()}"; $p['stack'][] = array('tag' => 'sub', 'string' => "{SUB}");
    						break;
						case "sup": $src .= "{SUP()}"; $p['stack'][] = array('tag' => 'sup', 'string' => "{SUP}");
    						break;
						case "tt" : $src .= '{DIV(type="tt")}'; $p['stack'][] = array('tag' => 'tt', 'string' => "{DIV}");
    						break;
						case "s"  : $src .= $this->processWikiTag('s', $src, $p, '--', '--', true);
    						break;
						// Table parser
						case "table": $src .= $this->startNewLine($src) . '||'; $p['stack'][] = array('tag' => 'table', 'string' => '||'); $p['first_tr'] = true;
    						break;
						case "tr": $src .= $p['first_tr'] ? '' : $this->startNewLine($src); $p['first_tr'] = false; $p['first_td'] = true;
    						break;
						case "td": $src .= $p['first_td'] ? '' : '|'; $p['first_td'] = false;
    						break;
						// Lists parser
						case "ul": $p['listack'][] = '*';
    						break;
						case "ol": $p['listack'][] = '#';
    						break;
						case "li":
							// Generate wiki list item according to current list depth.
							$src .=  $this->startNewLine($src) . str_repeat(end($p['listack']), count($p['listack']));
    						break;
						case "font":
							// If color attribute present in <font> tag
							if (isset($c[$i]["pars"]["color"]["value"])) {
								$src .= '~~'.$c[$i]["pars"]["color"]["value"].':';
								$p['stack'][] = array('tag' => 'font', 'string' => '~~');
							}
    						break;
						case "img":
							// If src attribute present in <img> tag
							if (isset($c[$i]["pars"]["src"]["value"]))
								// Note what it produce (img) not {img}! Will fix this below...
								if ( strstr($c[$i]["pars"]["src"]["value"], "http:") ) {
									$src .= '{img src="'.$c[$i]["pars"]["src"]["value"].'"}';
								} else {
									$src .= '{img src="'.$head_url.$c[$i]["pars"]["src"]["value"].'"}';
								}
    						break;
						case "a":
							if (isset($c[$i]['pars'])) {
								// get the link text
								$text = '';
								if ( $i < count($c) ) {
									$next_token = &$c[$i+1];
									if (isset($next_token['type']) && $next_token['type'] == 'text' && isset($next_token['data']) ) {
										$text = &$next_token['data'];
									}
								}
								// parse the link
								$this->parseLinkTag($c[$i]['pars'], $text, $src, $p);
							}

							// deactivated by mauriz, will be replaced by the routine above
							// If href attribute present in <a> tag
							/*
							if (isset($c[$i]["pars"]["href"]["value"])) {
								if ( strstr( $c[$i]["pars"]["href"]["value"], "http:" )) {
									$src .= '['.$c[$i]["pars"]["href"]["value"].'|';
								} else {
									$src .= '['.$head_url.$c[$i]["pars"]["href"]["value"].'|';
								}
								$p['stack'][] = array('tag' => 'a', 'string' => ']');
							}
							if ( isset($c[$i]["pars"]["name"]["value"])) {
								$src .= '{ANAME()}'.$c[$i]["pars"]["name"]["value"].'{ANAME}';
							}
							*/


    						break;
					}	// end switch on tag name
				} else {
					// This is close tag type. Is that smth we r waiting for?
					switch ($c[$i]["data"]["name"]) {
						case "ul":
							if (end($p['listack']) == '*') array_pop($p['listack']);
							if ( empty($p['listack']) )
								$src .= "\n";
    						break;
						case "ol":
							if (end($p['listack']) == '#') array_pop($p['listack']);
							if ( empty($p['listack']) )
								$src .= "\n";
							break;
						default:
							$e = end($p['stack']);
							if ($c[$i]["data"]["name"] == $e['tag']) {
								$src .= $e['string'];
								array_pop($p['stack']);
							}
							break;
					}

					// update the wiki stack
					if (isset($e['wikitags']) && $e['wikitags']) {
						for ( $i_wiki = 0; $i_wiki < $e['wikitags']; $i_wiki++ ) {
							array_pop($p['wikistack']);
						}
					}

					// can we leave wiki line break mode ?
					switch ($c[$i]["data"]["name"]) {
						case "a":
						case "h1":
						case "h2":
						case "h3":
						case "h4":
						case "h5":
						case "h6": $p['wiki_lbr']--;
							break;
					}
				}
			}
			// Recursive call on tags with content...
			if (isset($c[$i]["content"])) {
				if (substr($src, -1) != " ") $src .= " ";
				$this->walk_and_parse($c[$i]["content"], $src, $p, $head_url);
			}
		}
		if (substr($src, -2) == "\n\n") {	// seem to always get too many line ends
			$src = substr($src, 0, -2);
		}
	}	// end walk_and_parse

	function startNewLine(&$str)
	{
		if (strlen($str) && substr($str, -1) != "\n") {
			$str .=  "\n";
		}
	}
	/**
	 * wrapper around zaufi's HTML sucker code just to use the html to wiki bit
	 *
	 * \param &$c string -- HTML in
	 * \param &$src string -- output string
	 */


	function parse_html(&$inHtml)
	{
		$smarty = TikiLib::lib('smarty');

		include ('lib/htmlparser/htmlparser.inc');

		// Read compiled (serialized) grammar
		$grammarfile = 'lib/htmlparser/htmlgrammar.cmp';
		if (!$fp = @fopen($grammarfile, 'r')) {
			$smarty->assign('msg', tra("Can't parse HTML data - no grammar file"));
			$smarty->display("error.tpl");
			die;
		}
		$grammar = unserialize(fread($fp, filesize($grammarfile)));
		fclose($fp);

		// process a few ckeditor artifacts
		$inHtml = str_replace('<p></p>', '', $inHtml);	// empty p tags are invisible

		// create parser object, insert html code and parse it
		$htmlparser = new HtmlParser($inHtml, $grammar, '', 0);
		$htmlparser->Parse();
		// Should I try to convert HTML to wiki?
		$out_data = '';
		/*
		 * ['stack'] = array
		 * Speacial keys introduced to convert to Wiki
		 * - ['wikitags']     = the number of 'wikistack' entries produced by the html tag
		 *
		 * ['wikistack'] = array(), is used to save the wiki markup for the linebreak handling (1 array = 1 html tag)
		 * Each array entry contains the following keys:
		 * - ['begin']        = array() of begin markups (1 style definition = 1 array entry)
		 * - ['end']          = array() of end markups
		 *
		 * wiki_lbr  = true if we must use '%%%' for linebreaks instead of '\n'
		 */
		$p = array('stack' => array(), 'listack' => array(), 'wikistack' => array(),
			'wiki_lbr' => 0, 'first_td' => false, 'first_tr' => false);
		$this->walk_and_parse($htmlparser->content, $out_data, $p, '');
		// Is some tags still opened? (It can be if HTML not valid, but this is not reason
		// to produce invalid wiki :)
		while (count($p['stack'])) {
			$e = end($p['stack']);
			$out_data .= $e['string'];
			array_pop($p['stack']);
		}
		// Unclosed lists r ignored... wiki have no special start/end lists syntax....
		// OK. Things remains to do:
		// 1) fix linked images
		$out_data = preg_replace(',\[(.*)\|\(img src=(.*)\)\],mU', '{img src=$2 link=$1}', $out_data);
		// 2) fix remains images (not in links)
		$out_data = preg_replace(',\(img src=(.*)\),mU', '{img src=$1}', $out_data);
		// 3) remove empty lines
		$out_data = preg_replace(",[\n]+,mU", "\n", $out_data);
		// 4) remove nbsp's
		$out_data = preg_replace(",&#160;,mU", " ", $out_data);

		return $out_data;
	}	// end parse_html


	function get_new_page_attributes_from_parent_pages($page, $page_info)
	{
		$tikilib = TikiLib::lib('tiki');
		$wikilib = TikiLib::lib('wiki');

		$new_page_attrs = array();
		$parent_pages = $wikilib->get_parent_pages($page);
		$parent_pages_info = array();
		foreach ($parent_pages as $a_parent_page_name) {
			$this_parent_page_info = $tikilib->get_page_info($a_parent_page_name);
			$parent_pages_info[] = $this_parent_page_info;
		}
		$new_page_attrs = $this->get_newpage_language_from_parent_page($page, $page_info, $parent_pages_info, $new_page_attrs);
		// Note: in the future, may add some methods below to guess things like
		//       categories, workspaces, etc...

		return $new_page_attrs;
	}

	function get_newpage_language_from_parent_page($page, $page_info, $parent_pages_info, $new_page_attrs)
	{
		if (!isset($page_info['lang'])) {
			$lang = null;
			foreach ($parent_pages_info as $this_parent_page_info) {
				if (isset($this_parent_page_info['lang'])) {
					if ($lang != null and $lang != $this_parent_page_info['lang']) {
						// If more than one parent pages and they have different languages
						// then we can't guess which  is the right one.
						$lang = null;
						break;
					} else {
						$lang = $this_parent_page_info['lang'];
					}
				}
			}
			if ($lang != null) {
				$new_page_attrs['lang'] = $lang;
			}
		}
		return $new_page_attrs;
	}
}

