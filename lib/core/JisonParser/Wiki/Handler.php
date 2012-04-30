<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	var $parsing = false;
	public static $spareParsers = array();

	var $npOn = false;
	var $pluginStack = array();
	var $blockLoc = array();
	var $blockLast = '';
	var $blockStack = array();
	var $olistLen = array();

	function parse($input)
	{
		$result = "";

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
			$result = parent::parse($input);
			$this->parsing = false;
		}

		return $result;
	}

	// state & plugin handlers
	function plugin($pluginDetails)
	{
		$argParser = new WikiParser_PluginArgumentParser;

		return $this->parse( $this->pluginExecute(
			$pluginDetails['name'],
			$argParser->parse($pluginDetails['args']),
			$pluginDetails['body']
		));
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
		if ($condition != $this->blockLast)
			$this->blockLoc[$condition]++;

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
		
		return '<h' . $hNum . '>' . $content . '</h1>';
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
