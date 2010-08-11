<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
				$error_msg .= '<a href="'.$wikilib->editpage_url($an_alias['fromPage']).'">'.$an_alias['fromPage'].'</a>, ';
			}
			$error_msg .= "\n<p>\n";
			$error_msg .= tra("If you want to create the page, you must first edit each the pages above, and remove the alias link it may contain. This link should look something like this");
			$error_msg .= ": <b>(alias($page))</b>";
			require_once('lib/tikiaccesslib.php');
			$access->display_error(page, $error_title, "", true, $error_msg);
		}	
	}	
	
	function user_needs_to_specify_language_of_page_to_be_created($page, $page_info, $new_page_inherited_attributes = null) {
		global $_REQUEST, $multilinguallib, $prefs;
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

	function parseToWiki(&$inData) {
		global $prefs;
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			$parsed = $inData;
		} else {
			// Parsing page data as first time seeing html page in normal editor
			$parsed = $this->parse_html($inData);
		}
		$parsed = preg_replace('/\{img src=.*?img\/smiles\/.*? alt=([\w\-]*?)\}/im','(:$1:)', $parsed);	// "unfix" smilies
		$parsed = preg_replace('/%%%/m',"\n", $parsed);													// newlines
		return $parsed;
	}
	
	function parseToWysiwyg(&$inData) {
		global $tikilib, $tikiroot, $prefs;
		// Parsing page data as first time seeing wiki page in wysiwyg editor
		$parsed = preg_replace('/(!!*)[\+\-]/m','$1', $inData);		// remove show/hide headings
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			$parsed = $tikilib->parse_data($parsed,array('absolute_links'=>true, 'noparseplugins'=>true,'noheaderinc'=>true, 'fck' => 'y'));
		} else {
			$parsed = $tikilib->parse_data($parsed,array('absolute_links'=>true, 'noparseplugins'=>true,'noheaderinc'=>true));
		}
		$parsed = preg_replace('/<span class=\"img\">(.*?)<\/span>/im','$1', $parsed);					// remove spans round img's
		$parsed = preg_replace("/src=\"img\/smiles\//im","src=\"".$tikiroot."img/smiles/", $parsed);	// fix smiley src's
		$parsed = str_replace( 
				array( '{SUP()}', '{SUP}', '{SUB()}', '{SUB}', '<table' ),
				array( '<sup>', '</sup>', '<sub>', '</sub>', '<table border="1"' ),
				$parsed );
		return $parsed;
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
		// If no string
		if(!$c) { return; }
		
		for ($i=0; $i <= $c["contentpos"]; $i++) {
			// If content type 'text' output it to destination...
			if ($c[$i]["type"] == "text") {
				if( ! ctype_space( $c[$i]["data"] ) ) {
					$add = ltrim( $c[$i]["data"] );
					$add = str_replace( array("\r","\n"), ' ', $add );
					$src .= $add;
				}
			} elseif ($c[$i]["type"] == "comment") {
				$src .= preg_replace( '/<!--/', "\n~hc~", preg_replace( '/-->/', "~/hc~\n", $c[$i]["data"] ));
			} elseif ($c[$i]["type"] == "tag") {
				if ($c[$i]["data"]["type"] == "open") {
					// Open tag type
					switch ($c[$i]["data"]["name"]) {
						// Tags we don't want at all.
						case "meta": $c[$i]["content"] = ''; break;
						
						case "br": $src .= '%%%'; break;
						case "hr": $src .= '---'; break;
						case "title": $src .= "\n!"; $p['stack'][] = array('tag' => 'title', 'string' => "\n"); break;
						case "p":
						case "div": // Wiki parsing creates divs for center
							if( isset($c[$i]['pars']) 
								&& isset($c[$i]['pars']['style']) 
								&& $c[$i]['pars']['style']['value'] == 'text-align: center;' ) {
									if ($prefs['feature_use_three_colon_centertag'] == 'y') {
										$src .= "\n:::";
										$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => ":::\n");
									} else {
										$src .= "\n::";
										$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "::\n");
									}
							} else {
								$src .= "\n";
								$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "\n"); 
							}
							break;
						case "span":
							if( isset($c[$i]['pars']) 
								&& isset($c[$i]['pars']['style']) 
								&& preg_match( "/background(\-color)?: rgb\((\d+), (\d+), (\d+)\)/", $c[$i]['pars']['style']['value'], $parts ) ) {
								$src .= "~~#"
									. str_pad( dechex( 255-$parts[2] ), 2, '0', STR_PAD_LEFT )
									. str_pad( dechex( 255-$parts[3] ), 2, '0', STR_PAD_LEFT )
									. str_pad( dechex( 255-$parts[4] ), 2, '0', STR_PAD_LEFT )
									. ',#'
									. str_pad( dechex( $parts[2] ), 2, '0', STR_PAD_LEFT )
									. str_pad( dechex( $parts[3] ), 2, '0', STR_PAD_LEFT )
									. str_pad( dechex( $parts[4] ), 2, '0', STR_PAD_LEFT )
									. ':';
								$p['stack'][] = array('tag' => 'span', 'string' => "~~"); 
							} elseif( isset($c[$i]['pars']) 
								&& isset($c[$i]['pars']['style']) 
								&& preg_match( "/color: rgb\((\d+), (\d+), (\d+)\)/", $c[$i]['pars']['style']['value'], $parts ) ) {
								$src .= "~~#"
									. str_pad( dechex( $parts[1] ), 2, '0', STR_PAD_LEFT )
									. str_pad( dechex( $parts[2] ), 2, '0', STR_PAD_LEFT )
									. str_pad( dechex( $parts[3] ), 2, '0', STR_PAD_LEFT )
									. ':';
								$p['stack'][] = array('tag' => 'span', 'string' => "~~"); 
							}
							break;
						case "b": $src .= '__'; $p['stack'][] = array('tag' => 'b', 'string' => '__'); break;
						case "i": $src .= "''"; $p['stack'][] = array('tag' => 'i', 'string' => "''"); break;
						case "em": $src .= "''"; $p['stack'][] = array('tag' => 'em', 'string' => "''"); break;
						case "strong": $src .= '__'; $p['stack'][] = array('tag' => 'strong', 'string' => '__'); break;
						case "u": $src .= "=="; $p['stack'][] = array('tag' => 'u', 'string' => "=="); break;
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
						// headers detection looks like real suxx code...
						// but possible it run faster :) I don't know where is profiler in PHP...
						case "h1": $src .= "\n!"; $p['stack'][] = array('tag' => 'h1', 'string' => "\n"); break;
						case "h2": $src .= "\n!!"; $p['stack'][] = array('tag' => 'h2', 'string' => "\n"); break;
						case "h3": $src .= "\n!!!"; $p['stack'][] = array('tag' => 'h3', 'string' => "\n"); break;
						case "h4": $src .= "\n!!!!"; $p['stack'][] = array('tag' => 'h4', 'string' => "\n"); break;
						case "h5": $src .= "\n!!!!!"; $p['stack'][] = array('tag' => 'h5', 'string' => "\n"); break;
						case "h6": $src .= "\n!!!!!!"; $p['stack'][] = array('tag' => 'h6', 'string' => "\n"); break;
						case "pre": $src .= "~pre~\n"; $p['stack'][] = array('tag' => 'pre', 'string' => "~/pre~\n"); break;
						case "sub": $src .= "{SUB()}"; $p['stack'][] = array('tag' => 'sub', 'string' => "{SUB}"); break;
						case "sup": $src .= "{SUP()}"; $p['stack'][] = array('tag' => 'sup', 'string' => "{SUP}"); break;
						// Table parser
						case "table": $src .= '||'; $p['stack'][] = array('tag' => 'table', 'string' => '||'); $p['first_tr'] = true; break;
						case "tr": $src .= $p['first_tr'] ? '' : "\n"; $p['first_tr'] = false; $p['first_td'] = true; break;
						case "td": $src .= $p['first_td'] ? '' : '|'; $p['first_td'] = false; break;
						// Lists parser
						case "ul": $p['listack'][] = '*'; break;
						case "ol": $p['listack'][] = '#'; break;
						case "li":
							// Generate wiki list item according to current list depth.
							// (ensure '*/#' starts from begining of line)
							$temp_max = count($p['listack']);
							for ($l = ''; strlen($l) < $temp_max; $l .= end($p['listack']));	// needs strlen function in 2nd for loop argument
							$src .= "\n$l ";
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
									$src .= '(img src='.$c[$i]["pars"]["src"]["value"].')';
								} else {
									$src .= '(img src='.$head_url.$c[$i]["pars"]["src"]["value"].')';
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
	}	// end walk_and_parse
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
