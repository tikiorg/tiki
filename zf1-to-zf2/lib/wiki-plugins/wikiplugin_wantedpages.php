<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/wiki/pluginslib.php';

function wikiplugin_wantedpages_info()
{
	return array(
		'name' => tra('Wanted Pages'),
		'documentation' => 'PluginWantedPages',
		'description' => tra('Show location of links to pages not yet created'),
		'prefs' => array( 'wikiplugin_wantedpages' ),
		'body' => tr('Custom level regex. A custom filter for wanted pages to be listed (only used when %0). Possible
			values: a valid regex-expression (PCRE).', '<code>level="custom"</code>'),
		'iconname' => 'search',
		'introduced' => 1,
		'tags' => array( 'basic' ),
		'params' => array(
			'ignore' => array(
				'required' => false,
				'name' => tra('Ignore'),
				'description' => tra('A wildcard pattern of originating pages to be ignored. (refer to PHP function
					fnmatch() for details)'),
				'since' => '1',
				'accepted' => tra('a valid regex-expression (PCRE)'),
				'default' => '',
				'advanced' => true,
			),
			'splitby' => array(
				'required' => false,
				'name' => tra('Split By'),
				'description' => tra('The character by which ignored patterns are separated.'),
				'since' => '1',
				'default' => '+',
				'advanced' => true,
			),
			'skipalias' => array(
				'required' => false,
				'name' => tra('Skip Alias'),
				'description' => tra('Whether to skip wanted pages that have a defined alias (not skipped by default)'),
				'since' => '12.1',
				'default' => 0,
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'skipext' => array(
				'required' => false,
				'name' => tra('Skip Extension'),
				'description' => tra('Whether to include external wikis in the list (not included by default)'),
				'since' => '1',
				'default' => 0,
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'collect' => array(
				'required' => false,
				'name' => tra('Collect'),
				'description' => tra('Collect either originating (from) or wanted pages (to) in a cell and display them
					in the second column.'),
				'since' => '1',
				'default' => 'from',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('From'), 'value' => 'from'),
					array('text' => tra('To'), 'value' => 'to'),
				),
			),
			'debug' => array(
				'required' => false,
				'name' => tra('Debug'),
				'description' => tra('Switch on debug output with details about the items (debug not on by default)'),
				'since' => '1',
				'default' => 0,
				'filter' => 'digits',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => 0),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('Memory Saver'), 'value' => 2),
					),
			),
			'table' => array(
				'required' => false,
				'name' => tra('Table'),
				'description' => tra('Multiple collected items are separated in distinct table rows (default), or by
					comma or line break in one cell.'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'sep',
				'accepted' => 'sep, co, br',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Comma'), 'value' => 'co'),
					array('text' => tra('Line break'), 'value' => 'br'),
					array('text' => tra('Separate Row'), 'value' => 'sep'),
				),
			),
			'level' => array(
				'required' => false,
				'name' => tra('Level'),
				'description' => tra('Filter the list of wanted pages according to page_regex or custom filter. The
					default value is the site\'s __current__ page_regex.'),
				'since' => '1',
				'default' => '',
				'filter' => 'alpha',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Custom'), 'value' => 'custom'),
					array('text' => tra('Full'), 'value' => 'full'),
					array('text' => tra('Strict'), 'value' => 'strict'),
				),
			),
		),
	);
}

class WikiPluginWantedPages extends PluginsLib
{

	function getDefaultArguments()
	{
		return array(	'ignore' => '', // originating pages to be ignored
						'splitby' => '+', // split ignored pages by this character
						'skipalias' => 0, // false, count a page alias as a wanted page
						'skipext' => 0, // false, display external wiki links
						'collect' => 'from', // display (and sort) wanted pages in the first column,
						// collect originating pages in the second column (and separate them by table parameter)
						'table' => 'sep', // show each line of output in a separate table row
						'level' => '', // use current page_regex to filter output
						'debug' => 0); // false, no debug output; a value of 2
						    // tries to allocate as little memory as possible.
	}

	function getName ()
	{
		return 'WantedPages';
	}

	function getDescription ()
	{
		return wikiplugin_wantedpages_help();
	}

	function getVersion ()
	{
		return preg_replace("/[Revision: $]/", '', "\$Revision: 1.7 $");
	}

	function run($data, $params)
	{
		global $prefs, $page_regex;

		// Grab and handle our Tiki parameters...
		extract($params, EXTR_SKIP);
		if (!isset($ignore)) {
			$ignore = '';
		}
		if (!isset($splitby)) {
			$splitby = '+';
		}
		if (!isset($skipalias)) {
			$skipalias = false;
		}
		if (!isset($skipext)) {
			$skipext = false;
		}
		if (!isset($debug)) {
			$debug = false;
		}
		if (!isset($collect)) {
			$collect = 'from';
		}
		if (!isset($table)) {
			$table = 'sep';
		}
		if (!isset($level)) {
			$level = '';
		}

		// for regexes and external wiki details, see tikilib.php
		if ($level == 'strict') {
			$level_reg = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
		} elseif ($level == 'full') {
			$level_reg = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
		} elseif ($level == 'complete') {
			$level_reg = '([^|\(\)])([^|\(\)](?!\)\)))*?([^|\(\)])';
		} elseif (($level == 'custom') && ($data != '')) {
			if (preg_ispreg($data)) { // custom regular expression
				$level_reg = $data;
			} elseif ($debug == 2) {
				echo $data . ': ' . tra('non-valid custom regex') . '<br />';
			}
		} else { // default
			$level_reg = $page_regex;
		}

		// define the separator
		if ($table == 'br') {
			$break = '<br />';
		} elseif ($table == 'co') {
			$break = tra(', ');
		} else {
			$break = 'sep';
		}

		// get array of fromPages to be ignored
		$ignorepages = explode($splitby, $ignore);

		// Currently we only look in wiki pages.
		// Wiki links in articles, blogs, etc are ignored.
		$query = 'select distinct tl.`toPage`, tl.`fromPage` from `tiki_links` tl';
		$query .= ' left join `tiki_pages` tp on (tl.`toPage` = tp.`pageName`)';
		if ($skipalias) {
			$query .= ' left join `tiki_object_relations` tor on (tl.`toPage` = tor.`target_itemId`)';
		}

		$categories = $this->get_jail();
		if ($categories)
			$query .= ' inner join `tiki_objects` as tob on (tob.`itemId`= tl.`fromPage` and tob.`type`= ?) inner join `tiki_category_objects` as tc on (tc.`catObjectId`=tob.`objectId` and tc.`categId` IN(' . implode(', ', array_fill(0, count($categories), '?')) . '))';
		$query .= ' where tp.`pageName` is null';
		if ($skipalias) {
			$query .= ' and (tor.`relation` is null or tor.`relation` != \'tiki.link.alias\')';
		}
		$result = $this->query($query, $categories ? array_merge(array('wiki page'), $categories) : array());
		$tmp = array();

		while ($row = $result->fetchRow()) {
			foreach ($ignorepages as $ipage) {
				// test whether a substring ignores this page, ignore case
				if (fnmatch(TikiLib::strtolower($ipage), TikiLib::strtolower($row['fromPage'])) === true) {
					if ($debug == 2) { // the "hardcore case"
						echo $row['toPage'] . ' [from: ' . $row['fromPage'] . ']: ' . tra('ignored') . '<br />';
					} elseif ($debug) { // add this page to the table
						$tmp[] = array($row['toPage'], $row['fromPage'], 'ignored');
					}
					continue 2; // no need to test other ignorepages or toPages
				}
			} // foreach ignorepage

			// if toPage contains colon, and exloding yields two parts => external Wiki
			if (($skipext) && (strstr($row['toPage'], ':') !== false)) {
				$parts = explode(':', $row['toPage']);
				if (count($parts) == 2) {
					if ($debug == 2) {
						echo $row['toPage'] . ' [from: ' . $row['fromPage'] . ']: ' . tra('External Wiki') . '<br />';
					} elseif ($debug) {
						$tmp[] = array($row['toPage'], $row['fromPage'], 'External Wiki');
					}
					continue;
				}
			} // $skipext

			$dashWikiWord = preg_match("/^(?<=[ \n\t\r\,\;]|^)([A-Z][a-z0-9_\-\x80-\xFF]+[A-Z][a-z0-9_\-\x80-\xFF]+[A-Za-z0-9\-_\x80-\xFF]*)(?=$|[ \n\t\r\,\;\.])$/", $row['toPage']);
			$WikiWord = preg_match("/^(?<=[ \n\t\r\,\;]|^)([A-Z][a-z0-9\x80-\xFF]+[A-Z][a-z0-9\x80-\xFF]+[A-Za-z0-9\x80-\xFF]*)(?=$|[ \n\t\r\,\;\.])$/", $row['toPage']);
			// test whether toPage is a valid wiki page under current syntax
			if ($dashWikiWord && !$WikiWord) { // a Dashed-WikiWord, can we allow this?
				if (($prefs['feature_wikiwords'] != 'y') || ($prefs['feature_wikiwords_usedash'] != 'y')) {
					$tmp = debug_print($row, $debug, tra('dash-WikiWord'));
					continue;
				}
			} elseif ($WikiWord) { // a WikiWord, can we allow this?
				if ($prefs['feature_wikiwords'] != 'y') {
					$tmp = debug_print($row, $debug, tra('WikiWord'));
					continue;
				}
			} else { // no WikiWord, we can now filter with the level parameter
				if (!preg_match("/^($level_reg)$/", $row['toPage'])) {
					$tmp = debug_print($row, $debug, tra('not in level'));
					continue;
				}
			} // dashWikiWord, WikiWord, normal link

			if (!$debug) { // a normal, valid WantedPage:
				$tmp[] = array($row['toPage'], $row['fromPage']);
			} elseif ($debug == 2) {
				debug_print($row, $debug, tra('valid'));
			} // in simple debug mode, valid links are ignored
		} // while (each entry in tiki_links is handled)
		unset($result); // free memory

		if ($debug == 2) {
			return(tra('End of debug output.'));
		}

		$out = array();
		$linkin  = (!$debug) ? '((' : '~np~'; // this is how toPages are handled
		$linkout = (!$debug) ? '))' : '~/np~';
		foreach ($tmp as $row) { // row[toPage, fromPage, reason]
			if ($debug) { // modified rejected toPages with reason
				$row[0] = '<em>' . tra($row[2]) . '</em>: ' . $row[0];
			}
			$row[0] = $linkin . $row[0] . $linkout; // toPages
			$row[1] = '((' . $row[1] . '))'; // fromPages

			// two identical keys may exist, they can either be displayed
			// each in its own table row, or be collected in one cell, separated by
			// either comma or <br />
			if ($collect == 'from') {
				if ($break == 'sep') {
					// toPages separated in each row, there might be duplicates!!!
					$out[] = array($row[0], $row[1]);
				} elseif (!array_key_exists($row[0], $out)) {
					// multiple fromPages (for one toPage) might be in one row, this is the first
					$out[$row[0]] = $row[1];
				} else {
					// multiple fromPages might be in one row, this is a follow-up
					$out[$row[0]] = $out[$row[0]].$break.$row[1];
				}
			} else { // $collect == to
				if ($break == 'sep') {
					// fromPages separated in each row, there might be duplicates!!!
					$out[] = array($row[1], $row[0]);
				} elseif (!array_key_exists($row[1], $out)) {
					// multiple toPages (for one fromPage) might be in one row, this is the first
					$out[$row[1]] = $row[0];
				} else { // multiple toPages might be in one row, this is a follow-up
					$out[$row[1]] = $out[$row[1]] . $break . $row[0];
				}
			}
		} // foreach (received row) is handled
		unset($tmp); // free memory

		// sort the entries
		if ($break == 'sep') {
			sort($out);
		} else {
			ksort($out);
		}

		$headerwant = tra('Wanted Page');
		$headerref = tra('Referenced By Page');
		$rowbreak = "\n";
		$endtable = '||';
		if ($prefs['feature_wiki_tables'] != 'new') {
			$rowbreak = ' || ';
			$endtable = '';
		}

		$sOutput = '||' . '__';
		if ($collect == 'from') {
			$sOutput .= $headerwant . '__|__' . $headerref . '__' . $rowbreak;
			if ($break == 'sep') {
				foreach ($out as $link) {
					$sOutput .= $link[0] . ' | ' . $link[1] . $rowbreak;
				}
			} else {
				foreach ($out as $to => $from) {
					$sOutput .= $to . ' | ' . $from . $rowbreak;
				}
			}
		} else { // $collect == 'to'
			$sOutput .= $headerref . '__|__' . $headerwant . '__' . $rowbreak;
			if ($break == 'sep') {
				foreach ($out as $link) {
					$sOutput .= $link[0] . ' | ' . $link[1] . $rowbreak;
				}
			} else {
				foreach ($out as $from => $to) {
					$sOutput .= $from . ' | ' . $to . $rowbreak;
				}
			}
		}
		$sOutput .= $endtable;
		return $sOutput;
	} // run()
} // class WikiPluginWantedPages

function wikiplugin_wantedpages($data, $params)
{
  $plugin = new WikiPluginWantedPages();
  return $plugin->run($data, $params);
}

// fnmatch() is not defined on windows or PHP < 4.3.0!!
// From php help "fnmatch", http://www.php.net/manual/de/function.fnmatch.php
// comment by "soywiz at gmail dot com 26-Jul-2005 07:07" (as of Jan. 21 2006)
if (!function_exists('fnmatch')) {
	function fnmatch($pattern, $string)
	{
		for ($op = 0, $npattern = '', $n = 0, $l = strlen($pattern); $n < $l; $n++) {
			switch ($c = $pattern[$n]) {
				case '\\':
					$npattern .= '\\' . @$pattern[++$n];
					break;
				case '.': case '+': case '^': case '$': case '(': case ')': case '{': case '}': case '=': case '!': case '<': case '>': case '|':
					$npattern .= '\\' . $c;
					break;
				case '?': case '*':
					$npattern .= '.' . $c;
					break;
				case '[': case ']': default:
					$npattern .= $c;
					if ($c == '[') {
						$op++;
					} else if ($c == ']') {
						if ($op == 0) return false;
						$op--;
					}
					break;
			}
		}
	   if ($op != 0) return false;
	   return preg_match('/' . $npattern . '/i', $string);
	} // function fnmatch
} // !exists(fnmatch)

// A small function to determine whether a string is a [valid] preg expression.
// From php help "Regular Expression Functions (Perl-Compatible)", http://www.php.net/pcre/
// comment by "alexbodn at 012 dot n@t dot il 09-Jan-2006 11:45" (as of Jan. 21 2006)
if (!function_exists('preg_ispreg')) {
	function preg_ispreg($str)
	{
		$prefix = '';
		$sufix = '';
	    if ($str[0] != '^')
	        $prefix = '^';
	    if ($str[strlen($str) - 1] != '$')
	        $sufix = '$';
	    $estr = preg_replace("'^/'", "\\/", preg_replace("'([^/])/'", "\\1\\/", $str));
	    if (@preg_match("/".$prefix.$estr.$sufix."/", $str, $matches))
	        return strcmp($str, $matches[0]) != 0;
	    return true;
	} // function preg_ispreg
} //!exists(preg_ispreg)

if (!function_exists('debug_print')) {
	function debug_print($row, $debug, $message)
	{
		if ($debug == 2) {
			echo $row['toPage'] . ' [from: ' . $row['fromPage'] . ']: ' . $message . '<br />';
			return;
		} elseif ($debug) {
			$tmp[] = array($row['toPage'], $row['fromPage'], $message);
			return $tmp;
		}
	}
}
