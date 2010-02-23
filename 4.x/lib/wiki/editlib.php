<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization

/****
 * Initially just a collection of the functions dotted around tiki-editpage.php for v4.0
 * Started in the edit_fixup experimental branch - jonnybradley Aug 2009
 * 
 */

class EditLib {
	
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
	
	// translation functions
	
	function isNewTranslationMode() {
		global $prefs;
	
		return $prefs['feature_multilingual'] == 'y'
			&& isset( $_REQUEST['translationOf']  )
			&& ! empty( $_REQUEST['translationOf'] );
	}

	function isUpdateTranslationMode() {
		return isset( $_REQUEST['source_page'] )
			&& isset( $_REQUEST['oldver'] )
			&& isset( $_REQUEST['newver'] );
	}

	function parseToWiki(&$inData) {
		// Parsing page data as first time seeing html page in normal editor
		$parsed = '';
		$parsed = $this->parse_html($inData);
		$parsed = preg_replace('/\{img src=.*?img\/smiles\/.*? alt=([\w\-]*?)\}/im','(:$1:)', $parsed);	// "unfix" smilies
		$parsed = preg_replace('/%%%/m',"\n", $parsed);													// newlines
		return $parsed;
	}
	
	function parseToWysiwyg(&$inData) {
		global $tikilib, $tikiroot;
		// Parsing page data as first time seeing wiki page in wysiwyg editor
		$parsed = preg_replace('/(!!*)[\+\-]/m','$1', $inData);		// remove show/hide headings
		$parsed = $tikilib->parse_data($parsed,array('absolute_links'=>true, 'parseimgonly'=>true,'noheaderinc'=>true, 'suppress_icons' => true));
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
								$src .= "\n::";
								$p['stack'][] = array('tag' => $c[$i]['data']['name'], 'string' => "::\n"); 
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
						case "center": $src .= '::'; $p['stack'][] = array('tag' => 'center', 'string' => '::'); break;
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
	
}


global $editlib;
$editlib = new EditLib;
