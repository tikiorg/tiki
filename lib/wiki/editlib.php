<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
	
	// general
		
	function make_sure_page_to_be_created_is_not_an_alias($page, $page_info) {
		global $_REQUEST, $semanticlib, $access, $wikilib, $tikilib;
		require_once 'lib/wiki/semanticlib.php';
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
			$error_msg .= tra("If you want to create the page, you must first edit each the pages above, and remove the alias link it may contain. This link should look something like this");
			$error_msg .= ": <b>(alias($page))</b>";
			require_once('lib/tikiaccesslib.php');
			$access->display_error(page, $error_title, "", true, $error_msg);
		}	
	}	
	
	function user_needs_to_specify_language_of_page_to_be_created($page, $page_info, $new_page_inherited_attributes = null) {
		global $_REQUEST, $multilinguallib, $prefs, $tikilib;
		if ($prefs['feature_wikiapproval'] == 'y' && substr($page, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix'] && $tikilib->page_exists($tikilib->get_approved_page($page))) {
			return false;
		}
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
	
	function isTranslationMode() {
		return $this->isUpdateTranslationMode() || $this->isNewTranslationMode();
	}
	
	function isNewTranslationMode() {
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

	function isUpdateTranslationMode() {
		return isset( $_REQUEST['source_page'] )
			&& isset( $_REQUEST['oldver'] )
			&& (!isset($_REQUEST['is_new_translation']) || $_REQUEST['is_new_translation'] == 'n')
			&& isset( $_REQUEST['newver'] );
	}
	
	function prepareTranslationData() {
		global $_REQUEST, $tikilib, $smarty;
		$this->setTranslationSourceAndTargetPageNames();		
		$this->setTranslationSourceAndTargetVersions();
	}
	
	private function setTranslationSourceAndTargetPageNames() {
		global $_REQUEST, $smarty;
		
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
	
	private function setTranslationSourceAndTargetVersions() {
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

	function aTranslationWasSavedAs($complete_or_partial) {	
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
	
	function saveCompleteTranslation() {
		global $multilinguallib, $tikilib;
		
		$sourceInfo = $tikilib->get_page_info( $this->sourcePageName );
		$targetInfo = $tikilib->get_page_info( $this->targetPageName );
				
		$multilinguallib->propagateTranslationBits( 
			'wiki page',
			$sourceInfo['page_id'],
			$targetInfo['page_id'],
			$sourceInfo['version'],
			$targetInfo['version'] );
		$multilinguallib->deleteTranslationInProgressFlags($targetInfo['page_id'], $sourceInfo['lang']);		
	}
	
	function savePartialTranslation() {
		global $multilinguallib, $tikilib;

		$sourceInfo = $tikilib->get_page_info( $this->sourcePageName );
		$targetInfo = $tikilib->get_page_info( $this->targetPageName );
		
		$multilinguallib->addTranslationInProgressFlags($targetInfo['page_id'], $sourceInfo['lang']);
		
	}

	function parseToWiki( $inData ) {
		
		$parsed = $this->parse_html($inData);
		$parsed = preg_replace('/\{img\(? src=.*?img\/smiles\/icon_([\w\-]*?)\..*\}/im','(:$1:)', $parsed);	// "unfix" smilies
		$parsed = preg_replace('/%%%/m',"\n", $parsed);													// newlines
		$parsed = preg_replace('/&nbsp;/m',' ', $parsed);												// spaces
		// Put back htmlentities as normal char
		$parsed = htmlspecialchars_decode($parsed,ENT_QUOTES);
		return $parsed;
	}
	
	function parseToWysiwyg( $inData, $fromWiki = false ) {
		global $tikilib, $tikiroot, $prefs;
		// Parsing page data for wysiwyg editor
		$inData = $this->partialParseWysiwygToWiki($inData);	// remove any wysiwyg plugins so they don't get double parsed
		$parsed = preg_replace('/(!!*)[\+\-]/m','$1', $inData);		// remove show/hide headings
		$parsed = preg_replace('/&#039;/', '\'', $parsed);			// catch single quotes at html entities
		
		$parsed = $tikilib->parse_data( $parsed, array( 'absolute_links'=>true, 'noheaderinc'=>true, 'suppress_icons' => true,
														'ck_editor' => true, 'is_html' => ($prefs['wysiwyg_htmltowiki'] === 'n' && !$fromWiki),
														'process_wiki_paragraphs' => ($prefs['wysiwyg_htmltowiki'] === 'y' || $fromWiki)));
		
		if ($prefs['wysiwyg_htmltowiki'] === 'n' && $fromWiki) {
			$parsed = preg_replace('/^\s*<p>&nbsp;[\s]*<\/p>\s*/iu','', $parsed);						// remove added empty <p>
		}
		$parsed = preg_replace('/<span class=\"img\">(.*?)<\/span>/im','$1', $parsed);					// remove spans round img's
		// Workaround Wysiwyg Image Plugin Editor in IE7 erases image on insert http://dev.tiki.org/item3615 
		$parsed2 = preg_replace('/(<span class=\"tiki_plugin\".*?plugin=\"img\".*?><\/span>)<\/p>/is','$1<span>&nbsp;</span></p>', $parsed);
		if ($parsed2 !== null) {
			$parsed = $parsed2;
		}
		// Fix IE7 wysiwyg editor always adding absolute path
		$search = '/(<a[^>]+href=\")https?\:\/\/' . preg_quote($_SERVER['HTTP_HOST'].$tikiroot, '/') . '([^>]+_cke_saved_href)/i'; 
		$parsed = preg_replace($search, '$1$2', $parsed);

		return $parsed;
	}
	
	/**
	 * Converts wysiwyg plugins into wiki.
	 * Also processes headings by removing surrounding <p> (possibly for wysiwyg_wiki_semi_parsed but not tested)  
	 * 
	 * @param string $inData (page data - mostly html but can have a bit of wiki in it)
	 */
	function partialParseWysiwygToWiki( $inData ) {

		// de-protect ck_protected comments
		$ret = preg_replace('/<!--{cke_protected}{C}%3C!%2D%2D%20end%20tiki_plugin%20%2D%2D%3E-->/i', '<!-- end tiki_plugin -->', $inData);
		// remove the wysiwyg plugin elements leaving the syntax only remaining
		$ret = preg_replace('/<(?:div|span)[^>]*syntax="(.*)".*end tiki_plugin --><\/(?:div|span)>/Umis', "$1", $ret);
		// preg_replace blows up here with a PREG_BACKTRACK_LIMIT_ERROR on pages with "corrupted" plugins
		if (!$ret) { $ret = $inData; }
		
		// take away the <p> that f/ck introduces around wiki heading ! to have maketoc/edit section working
		$ret = preg_replace('/<p>!(.*)<\/p>/iu', "!$1\n", $ret);
		
		// strip totally empty <p> tags generated in ckeditor 3.4
		$ret = preg_replace('/\s*<p>[\s]*<\/p>\s*/iu', "$1\n", $ret);
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
	function walk_and_parse(&$c, &$src, &$p, $head_url ) {
		global $prefs;
		// If no string
		if(!$c) { return; }
		
		for ($i=0; $i <= $c["contentpos"]; $i++) {
			// If content type 'text' output it to destination...
			if ($c[$i]["type"] == "text") {
				if( ! ctype_space( $c[$i]["data"] ) ) {
					$add = $c[$i]["data"];
					$add = str_replace( array("\r","\n"), '', $add );
					$add = str_replace( '&nbsp;', ' ', $add );
					$add = ltrim( $add );
					$src .= $add;
				}
			} elseif ($c[$i]["type"] == "comment") {
				$src .= preg_replace( '/<!--/', "\n~hc~", preg_replace( '/-->/', "~/hc~\n", $c[$i]["data"] ));
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
							} else if ($c[$j]['data']['name'] == 'br' && $more_spans === 1 && $other_elements === 0) {
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
					
					switch ($c[$i]["data"]["name"]) {
						// Tags we don't want at all.
						case "meta": $c[$i]["content"] = ''; break;
						
						// others we do want
						case "br": $src .= "\n"; break;
						case "hr": $src .= $this->startNewLine($src) . '---'; break;
						case "title": $src .= "\n!"; $p['stack'][] = array('tag' => 'title', 'string' => "\n"); break;
						case "p":
						case "div": // Wiki parsing creates divs for center
							if(isset($c[$i]['pars']['style']['value'])) {
								if ( strpos($c[$i]['pars']['style']['value'],'text-align: center;') !== false ) {
									if ($prefs['feature_use_three_colon_centertag'] == 'y') {
										$src .= $this->startNewLine($src) .":::";
										$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => ":::\n\n");
									} else {
										$src .= $this->startNewLine($src) . "::";
										$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "::\n\n");
									}
								} else if ( strpos($c[$i]['pars']['style']['value'],'text-align: right;') !== false ){
										$src .= $this->startNewLine($src) .'{DIV(type="p",align="right")}';
										$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "{DIV}\n\n");
								}
							} else {	// normal para or div
								$src .= $this->startNewLine($src);
								$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "\n\n"); 
							}
							break;
						case "span":
							if( isset($c[$i]['pars'])) {
								if (isset($c[$i]['pars']['style'])) {	// colours
									$contrast = '000000';
									if (preg_match( "/background(\-color)?: rgb\((\d+), (\d+), (\d+)\)/", $c[$i]['pars']['style']['value'], $parts ) ) {
										$bgcol = str_pad( dechex( $parts[2] ), 2, '0', STR_PAD_LEFT )
											   . str_pad( dechex( $parts[3] ), 2, '0', STR_PAD_LEFT )
											   . str_pad( dechex( $parts[4] ), 2, '0', STR_PAD_LEFT );
										
									} else if (preg_match( "/background(\-color)?:\s*#(\w{3,6})/", $c[$i]['pars']['style']['value'], $parts ) ) {
										$bgcol = $parts[2];
									}
									if (preg_match( "/\bcolor: rgb\((\d+), (\d+), (\d+)\)/", $c[$i]['pars']['style']['value'], $parts ) ) {
										$fgcol = str_pad( dechex( $parts[1] ), 2, '0', STR_PAD_LEFT )
											   . str_pad( dechex( $parts[2] ), 2, '0', STR_PAD_LEFT )
											   . str_pad( dechex( $parts[3] ), 2, '0', STR_PAD_LEFT );
									} else if (preg_match( "/^color:\s*#(\w{3,6})/", $c[$i]['pars']['style']['value'], $parts ) ) {
										$fgcol = $parts[1];
									}
									if (!empty($bgcol) || !empty($fgcol)) {
										$src .= "~~#" . (!empty($fgcol) ? $fgcol : $contrast);
										$src .= (empty($bgcol) ? '' : ',#' . $bgcol);
										$src .= ':';
										$p['stack'][] = array('tag' => 'span', 'string' => "~~"); 
									}
								}
							}
							break;
						case "b": $src .= '__'; $p['stack'][] = array('tag' => 'b', 'string' => '__'); break;
						case "i": $src .= "''"; $p['stack'][] = array('tag' => 'i', 'string' => "''"); break;
						case "em": $src .= "''"; $p['stack'][] = array('tag' => 'em', 'string' => "''"); break;
						case "strong": $src .= '__'; $p['stack'][] = array('tag' => 'strong', 'string' => '__'); break;
						case "u": $src .= "==="; $p['stack'][] = array('tag' => 'u', 'string' => "==="); break;
						case "strike": $src .= "--"; $p['stack'][] = array('tag' => 'strike', 'string' => "--"); break;
						case "del": $src .= "--"; $p['stack'][] = array('tag' => 'del', 'string' => "--"); break;
						case "center":
							if ($prefs['feature_use_three_colon_centertag'] == 'y') {
								$src .= ':::';
								$p['stack'][] = array('tag' => 'center', 'string' => ':::');
							} else {
								$src .= '::';
								$p['stack'][] = array('tag' => 'center', 'string' => '::');
							}
							break;
						case "code": $src .= '-+'; $p['stack'][] = array('tag' => 'code', 'string' => '+-'); break;
						case "dd": $src .= ':'; $p['stack'][] = array('tag' => 'dd', 'string' => "\n"); break;
						case "dt": $src .= ';'; $p['stack'][] = array('tag' => 'dt', 'string' => ''); break;
						
						case "h1":
						case "h2":
						case "h3":
						case "h4":
						case "h5":
						case "h6":
							$hlevel = (int) $c[$i]["data"]["name"]{1};
							if (isset($c[$i]['pars']['style']['value']) && strpos($c[$i]['pars']['style']['value'],'text-align: center;') !== false ) {
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
						case "pre": $src .= "~pre~\n"; $p['stack'][] = array('tag' => 'pre', 'string' => "~/pre~\n"); break;
						case "sub": $src .= "{SUB()}"; $p['stack'][] = array('tag' => 'sub', 'string' => "{SUB}"); break;
						case "sup": $src .= "{SUP()}"; $p['stack'][] = array('tag' => 'sup', 'string' => "{SUP}"); break;
						// Table parser
						case "table": $src .= $this->startNewLine($src) . '||'; $p['stack'][] = array('tag' => 'table', 'string' => '||'); $p['first_tr'] = true; break;
						case "tr": $src .= $p['first_tr'] ? '' : $this->startNewLine($src); $p['first_tr'] = false; $p['first_td'] = true; break;
						case "td": $src .= $p['first_td'] ? '' : '|'; $p['first_td'] = false; break;
						// Lists parser
						case "ul": $p['listack'][] = '*'; break;
						case "ol": $p['listack'][] = '#'; break;
						case "li":
							// Generate wiki list item according to current list depth.
							$src .=  $this->startNewLine($src) . str_repeat( end($p['listack']), count($p['listack']));
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
								if( strstr( $c[$i]["pars"]["src"]["value"], "http:" ) ) {
									$src .= '{img src="'.$c[$i]["pars"]["src"]["value"].'"}';
								} else {
									$src .= '{img src="'.$head_url.$c[$i]["pars"]["src"]["value"].'"}';
								}
							break;
						case "a":
							// If href attribute present in <a> tag
							if (isset($c[$i]["pars"]["href"]["value"])) {
								if( strstr( $c[$i]["pars"]["href"]["value"], "http:" )) {
									$src .= '['.$c[$i]["pars"]["href"]["value"].'|';
								} else {
									$src .= '['.$head_url.$c[$i]["pars"]["href"]["value"].'|';
								}
								$p['stack'][] = array('tag' => 'a', 'string' => ']');
							}
							if( isset($c[$i]["pars"]["name"]["value"])) {
								$src .= '{ANAME()}'.$c[$i]["pars"]["name"]["value"].'{ANAME}';
							}
							break;
					}	// end switch on tag name
				} else {
					// This is close tag type. Is that smth we r waiting for?
					switch ($c[$i]["data"]["name"]) {
						case "ul":
							if (end($p['listack']) == '*') array_pop($p['listack']);
							if( empty($p['listack']) )
								$src .= "\n";
							break;
						case "ol":
							if (end($p['listack']) == '#') array_pop($p['listack']);
							if( empty($p['listack']) )
								$src .= "\n";
							break;
						default:
							$e = end($p['stack']);
							if ($c[$i]["data"]["name"] == $e['tag'])
							{
								$src .= $e['string'];
								array_pop($p['stack']);
							}
							break;
					}
				}
			}
			// Recursive call on tags with content...
			if (isset($c[$i]["content"])) {
	//			if (substr($src, -1) != " ") $src .= " ";
				$this->walk_and_parse($c[$i]["content"], $src, $p, $head_url );
			}
		}
		if (substr($src, -2) == "\n\n") {	// seem to always get too many line ends
			$src = substr($src, 0, -2);
		}
	}	// end walk_and_parse
	
	function startNewLine(&$str) {
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
	
	
	function parse_html(&$inHtml) {
		global $smarty;

		include ('lib/htmlparser/htmlparser.inc');
	
		// Read compiled (serialized) grammar
		$grammarfile = 'lib/htmlparser/htmlgrammar.cmp';
		if (!$fp = @fopen($grammarfile,'r'))
		{
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
		$p = array('stack' => array(), 'listack' => array(), 'first_td' => false, 'first_tr' => false);
		$this->walk_and_parse( $htmlparser->content, $out_data, $p, '' );
		// Is some tags still opened? (It can be if HTML not valid, but this is not reason
		// to produce invalid wiki :)
		while (count($p['stack']))
		{
			$e = end($p['stack']);
			$out_data .= $e['string'];
			array_pop($p['stack']);
		}
		// Unclosed lists r ignored... wiki have no special start/end lists syntax....
		// OK. Things remains to do:
		// 1) fix linked images
		$out_data = preg_replace(',\[(.*)\|\(img src=(.*)\)\],mU','{img src=$2 link=$1}', $out_data);
		// 2) fix remains images (not in links)
		$out_data = preg_replace(',\(img src=(.*)\),mU','{img src=$1}', $out_data);
		// 3) remove empty lines
		$out_data = preg_replace(",[\n]+,mU","\n", $out_data);
		// 4) remove nbsp's
		$out_data = preg_replace(",&#160;,mU"," ", $out_data);
		
		return $out_data;
	}	// end parse_html

	function get_new_page_attributes_from_parent_pages($page, $page_info) {
		global $wikilib, $tikilib;
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

	function get_newpage_language_from_parent_page($page, $page_info, $parent_pages_info, $new_page_attrs) {
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


global $editlib;
$editlib = new EditLib;
