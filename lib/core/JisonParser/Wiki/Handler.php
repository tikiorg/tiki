<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	var $parsing = false;
	public static $hdrCount = 0;
	public static $spareParsers = array();

	var $npOn = false;
	var $pluginStack = array();
	public static $pluginCount = 0;
	var $blockLoc = array();
	var $blockLast = '';
	var $blockStack = array();
	var $olistLen = array();

	var $npEntries = array();
	public static $npCount = 0;
	var $pluginEntries = array();
	var $options = array();
	var $addLineBreaksTracking = array(
		'inTable' => 0,
		'inPre' => 0,
		'inComment' => 0,
		'inTOC' => 0,
		'inScript' => 0,
		'inDiv' => 0,
		'inHeader' => 0
	);

	function parse($input)
	{
		if ($this->parsing == true) {
			$parser = end(self::$spareParsers);
			if (!empty($parser) && $parser->parsing == false) {
				$result = $parser->parse($input);
			} else {
				self::$spareParsers[] = $parser = new JisonParser_Wiki_Handler();
				$result = $parser->parse($input);
			}
		} else {
			$this->parsing = true;

			$this->preParse($input);
			$result = parent::parse($input);
			$this->postParse($result);

			$this->parsing = false;
		}

		return $result;
	}

	function preParse(&$input)
	{
		$input = preg_replace_callback('/~np~(.|\n)*?~\/np~/', array(&$this, 'removeNpEntities'), $input);
	}

	function postParse(&$input)
	{
		$lines = explode("\n", $input);

		$ul = '';
		$listbeg = array();
		foreach($lines as &$line) {
			$this->parseLists($line, $listbeg, $ul);
			$this->addLineBreaks($line);
		}
		$input = implode("\n", $lines);

		$this->restoreNpEntities($input);
		$this->restorePluginEntities($input);
	}

	// state & plugin handlers
	function plugin($pluginDetails)
	{
		$argParser = new WikiParser_PluginArgumentParser;

		$key = '§' . md5('plugin:'.self::$pluginCount) . '§';
		self::$pluginCount++;

		$this->pluginEntries[$key] = $this->parse( $this->pluginExecute(
			$pluginDetails['name'],
			$argParser->parse($pluginDetails['args']),
			$pluginDetails['body']
		));

		return $key;
	}

	function stackPlugin($yytext)
	{
		$pluginName = $this->match('/^\{([A-Z]+)/', $yytext);
		$pluginArgs = rtrim(str_replace('{' . $pluginName . '(', '', $yytext), ')}');

		$this->pluginStack[] = array(
			'name' => $pluginName,
			'args' => $pluginArgs,
			'body' => ''
		);
	}

	function inlinePlugin($yytext)
	{
		$pluginName = $this->match('/^\{([a-z]+)/', $yytext);
		$pluginArgs = rtrim(str_replace('{'.$pluginName .' ', '', $yytext), '}');

		return array(
			'name' => $pluginName,
			'args' => $pluginArgs,
			'body' => ''
		);
	}

	function isPlugin()
	{
		return (count($this->pluginStack) > 0);
	}

	function pluginExecute($name, $args = array(), $body = "")
	{
		$fnName = strtolower('wikiplugin_' .  $name);

		if ( $this->pluginExists($name) && function_exists($fnName) ) {

			$result = $fnName($body, $args);

			return $result;
		}

		return $body;
	}

	function pluginExists($name)
	{
		$phpName = 'lib/wiki-plugins/wikiplugin_';
		$phpName .= strtolower($name) . '.php';

		$exists = file_exists($phpName);

		if ( $exists ) {
			include_once $phpName;
		}

		if ( $exists ) {
			return true;
		}

		return false;
	}

	function removeNpEntities(&$matches)
	{
		$key = '§' . md5('np:'.self::$npCount) . '§';
		$this->npEntries[$key] = substr($matches[0], 4, -5);
		self::$npCount++;
		return $key;
	}

	function restoreNpEntities(&$input)
	{
		foreach($this->npEntries as $key => $entity) {
			$input = str_replace($key, $entity, $input);
			unset($this->npEntries[$key]);
		}

		$this->npEntries = array();
	}

	function restorePluginEntities(&$input)
	{
		//use of array_reverse, jison is a reverse bottom-up parser, if it doesn't reverse jison doesn't restore the plugins in the right order, leaving the some nested keys as a result
		foreach(array_reverse($this->pluginEntries) as $key => $entity) {
			$input = str_replace($key, $entity, $input);
			unset($this->pluginEntries[$key]);
		}
	}

	function addLineBreaks(&$line)
	{
		$lineInLowerCase = TikiLib::strtolower($line);

		$this->addLineBreaksTracking['inComment'] += substr_count($lineInLowerCase, "<!--");
		$this->addLineBreaksTracking['inComment'] -= substr_count($lineInLowerCase, "-->");

		// check if we are inside a ~pre~ block and, if so, ignore
		// monospaced and do not insert <br />
		$this->addLineBreaksTracking['inPre'] += substr_count($lineInLowerCase, "<pre");
		$this->addLineBreaksTracking['inPre'] -= substr_count($lineInLowerCase, "</pre");

		// check if we are inside a table, if so, ignore monospaced and do
		// not insert <br />

		$this->addLineBreaksTracking['inTable'] += substr_count($lineInLowerCase, "<table");
		$this->addLineBreaksTracking['inTable'] -= substr_count($lineInLowerCase, "</table");

		// check if we are inside an ul TOC list, if so, ignore monospaced and do
		// not insert <br />
		$this->addLineBreaksTracking['inTOC'] += substr_count($lineInLowerCase, "<ul class=\"toc");
		$this->addLineBreaksTracking['inTOC'] -= substr_count($lineInLowerCase, "</ul><!--toc-->");

		// check if we are inside a script not insert <br />
		$this->addLineBreaksTracking['inScript'] += substr_count($lineInLowerCase, "<script ");
		$this->addLineBreaksTracking['inScript'] -= substr_count($lineInLowerCase, "</script");

		// check if we are inside a script not insert <br />
		$this->addLineBreaksTracking['inDiv'] += substr_count($lineInLowerCase, "<div ");
		$this->addLineBreaksTracking['inDiv'] -= substr_count($lineInLowerCase, "</div");

		// check if we are inside a script not insert <br />
		$this->addLineBreaksTracking['inHeader'] += substr_count($lineInLowerCase, "<h");
		$this->addLineBreaksTracking['inHeader'] -= substr_count($lineInLowerCase, "</h");

		if (
			$this->addLineBreaksTracking['inTable'] == 0 &&
			$this->addLineBreaksTracking['inPre'] == 0 &&
			$this->addLineBreaksTracking['inComment'] == 0 &&
			$this->addLineBreaksTracking['inTOC'] == 0 &&
			$this->addLineBreaksTracking['inScript'] == 0 &&
			$this->addLineBreaksTracking['inDiv'] == 0 &&
			$this->addLineBreaksTracking['inHeader'] == 0
		) {
			$line .= '<br />';
		}
	}

	function parseLists(&$line = "", &$listbeg = array(), &$data = '')
	{
		global $tikilib;
		$isStart = empty($data);

		$litype = substr($line, 0, 1);
		if (($litype == '*' || $litype == '#') && !(strlen($line)-count($listbeg)>4 && preg_match('/^\*+$/', $line))) {
			$listlevel = $tikilib->how_many_at_start($line, $litype);
			$liclose = '</li>';
			$addremove = 0;
			if ($listlevel < count($listbeg)) {
				while ($listlevel != count($listbeg)) $data .= array_shift($listbeg);
				if (substr(current($listbeg), 0, 5) != '</li>') $liclose = '';
			} elseif ($listlevel > count($listbeg)) {
				$listyle = '';
				while ($listlevel != count($listbeg)) {
					array_unshift($listbeg, ($litype == '*' ? '</ul>' : '</ol>'));
					if ($listlevel == count($listbeg)) {
						$listate = substr($line, $listlevel, 1);
						if (($listate == '+' || $listate == '-') && !($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>'))) {
							$thisid = 'id' . microtime() * 1000000;
							if ( !$this->options['ck_editor'] ) {
								$data .= '<br /><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
							}
							$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' || $this->options['ck_editor'] ? 'block' : 'none') . ';"';
							$addremove = 1;
						}
					}
					$data.=($litype=='*'?"<ul$listyle>":"<ol$listyle>");
				}
				$liclose='';
			}
			if ($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>')) {
				$data .= array_shift($listbeg);
				$listyle = '';
				$listate = substr($line, $listlevel, 1);
				if (($listate == '+' || $listate == '-')) {
					$thisid = 'id' . microtime() * 1000000;
					if ( !$this->options['ck_editor'] ) {
						$data .= '<br /><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
					}
					$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' || $this->options['ck_editor'] ? 'block' : 'none') . ';"';
					$addremove = 1;
				}
				$data .= ($litype == '*' ? "<ul$listyle>" : "<ol$listyle>");
				$liclose = '';
				array_unshift($listbeg, ($litype == '*' ? '</li></ul>' : '</li></ol>'));
			}
			$line = $liclose . '<li>' . substr($line, $listlevel + $addremove);
			if (substr(current($listbeg), 0, 5) != '</li>') array_unshift($listbeg, '</li>' . array_shift($listbeg));
		} elseif ($litype == '+') {
			$listlevel = TikiLib::how_many_at_start($line, $litype);
			// Close lists down to requested level
			while ($listlevel < count($listbeg)) $data .= array_shift($listbeg);

			// Must append paragraph for list item of given depth...
			$listlevel = TikiLib::how_many_at_start($line, $litype);
			if (count($listbeg)) {
				if (substr(current($listbeg), 0, 5) != '</li>') {
					array_unshift($listbeg, '</li>' . array_shift($listbeg));
					$liclose = '<li>';
				} else $liclose = '<br />';
			} else $liclose = '';
			$line = $liclose . substr($line, count($listbeg));

		} else {
			//we are either at the end of a list, or in a regular line
			$line = implode($listbeg) . $line;
			$listbeg =  array();
		}

		if ($isStart) {
			//We know we are at the start of an UL, so prepend it
			$line = $data . $line;
			$data = '';
		}
	}

	function SOL() //start of line
	{
		return ($this->yyloc['first_column'] == 0 ? true : false);
	}

	function popAllStates()
	{
		$this->conditionStackCount = 0;
		$this->conditionStack = array();
	}

	function beginBlock($condition)
	{
		if ($condition != $this->blockLast) {
			if (empty($this->blockLoc[$condition])) $this->blockLoc[$condition] = 0;
			$this->blockLoc[$condition]++;
		}

		$this->blockLast = $condition;

		return parent::begin($condition);
	}

	function newLine()
	{
		return '<br />';
	}
	//end state handlers
	//Wiki Syntax Objects Parsing Start
	function bold($content)
	{
		return '<strong>' . $content . '</strong>';
	}

	function box($content)
	{
		return'<div style="border: solid 1px black;">' . $content . '</div>';
	}

	function center($content)
	{
		return '<center>' . $content . '</center>';
	}

	function colortext($content)
	{
		$text = $this->split(':', $content);
		$color = $text[0];
		$content = $text[1];

		return '<span style="color: #' . $color . ';">' . $content . '</span>';
	}

	function content($content)
	{
		return $content;
	}

	function italics($content)
	{
		return '<i>' . $content . '</i>';
	}

	function header($content)
	{
		include_once('lib/smarty_tiki/function.icon.php');

		$hNum = 1;
		$headerLength = strlen($content);
		for($i = 0; $i < $headerLength; $i++) {
			if ($content[$i] == '!') {
				$hNum++;
			} else {
				break;
			}
		}

		$content = substr($content, $hNum - 1);



		return '<h' . $hNum . '>' . $content . $this->headerButton() . '</h' . $hNum . '>';
	}

	function headerButton()
	{
		global $prefs, $smarty;
		if ($prefs['wiki_edit_icons_toggle'] == 'y' && !isset($_COOKIE['wiki_plugin_edit_view'])) {
			$iconDisplayStyle = ' style="display:none;"';
		} else {
			$iconDisplayStyle = '';
		}
		$button = '<div class="icon_edit_section"' . $iconDisplayStyle . '><a href="tiki-editpage.php?';

		if (!empty($_REQUEST['page'])) {
			$button .= 'page='.urlencode($_REQUEST['page']).'&amp;';
		}

		self::$hdrCount++;

		$button .= 'hdr=' . self::$hdrCount . '">'.smarty_function_icon(array('_id'=>'page_edit_section', 'alt'=>tra('Edit Section')), $smarty).'</a></div>';

		return $button;
	}

	function hr()
	{
		return '<hr />';
	}

	function link($content)
	{
		$link = $this->split('|', $content);
		$href = (isset($link[0]) ? $link[0] : $content);
		$text = (isset($link[1]) ? $link[1] : $href);

		return '<a href="' . $href . '">' . $text . '</a>';
	}

	function smile($smile)
	{ //this needs more tlc too
		return '<img src="img/smiles/icon_' . $smile . '.gif" alt="' . $smile . '" />';
	}

	function strikethrough($content)
	{
		return '<span style="text-decoration: line-through;">' . $content . '</span>';
	}

	function tableParser($content)
	{
		$tableContents = '';
		$rows = $this->split('<br />', $content);

		for ($i = 0, $count_rows = count($rows); $i < $count_rows; $i++) {
			$row = '';

			$cells = $this->split('|', $rows[$i]);
			for ($j = 0, $count_cells = count($cells); $j < $count_cells; $j++) {
				$row .= $this->table_td($cells[$j]);
			}
			$tableContents .= $this->table_tr($row);
		}

		return '<table style="width: 100%;">' . $tableContents . '</table>';
	}

	function table_tr($content)
	{
		return '<tr>' . $content . '</tr>';
	}

	function table_td($content)
	{
		return '<td>' . $content . '</td>';
	}

	function titlebar($content)
	{
		return '<div class="titlebar">' . $content . '</div>';
	}

	function olist($content)
	{
		$this->olistLen[$this->blockLoc['olist']]++;
		$start =$this->olistLen[$this->blockLoc['olist']];

		return '<ol class="olgroup' . $this->blockLoc['olist'] . '" start="' . $start . '"><li>' . $content . '</li></ol>';
	}

	function ulist($content)
	{
		return '<ul><li>' . $content . '</li></ul>';
	}

	function underscore($content)
	{
		return '<u>' . $content . '</u>';
	}

	function wikilink($content)
	{
		$wikilink = $this->split('|', $content);
		$href = $content;

		if ($this->match('/\|/', $content)) {
			$href = $wikilink[0];
			$content = $wikilink[1];
		}

		return '<a href="' . $href . '">' . $content . '</a>';
	}

	function html($content)
	{
		return $content;
	}

	function formatContent($content)
	{
		//return nl2br($content);
		return $content;
	}

	//unified functions used inside parser
	function substring($val, $left, $right)
	{
		 return substr($val, $left, $right);
	}

	function match($pattern, $subject)
	{
		preg_match($pattern, $subject, $match);

		return (!empty($match[1]) ? $match[1] : false);
	}

	function replace($search, $replace, $subject)
	{
		return str_replace($search, $replace, $subject);
	}

	function split($delimiter, $string)
	{
		return explode($delimiter, $string);
	}

	function join()
	{
		$array = func_get_args();

		return implode($array, '');
	}

	function shift($array)
	{
		if (empty($array))
			$array = array();

		array_shift($array);

		return $array;
	}
}
