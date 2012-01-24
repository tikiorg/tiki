<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	private $parser;
	private $pre_handlers = array();
	private $pos_handlers = array();
	private $postedit_handlers = array();
	var $npOn = false;
	var $pluginStack = array();
	
	// state & plugin handlers
	function plugin($pluginDetails)
	{
		//nested parsing!
		$parserlib = new ParserLib;
		$parser = new JisonParser_Wiki_Handler();
		
		return $parser->parse(
			$parserlib->plugin_execute(
				$pluginDetails['name'],
				$pluginDetails['body'],
				$parserlib->plugin_split_args(
					$pluginDetails['args']
				)
			)
		);
	}
	
	function stackPlugin($yytext)
	{
		$pluginName = $this->match('/^\{([A-Z]+)/', $yytext);
		$pluginArgs =  $this->match('/[(].*?[)]/', $yytext);
		
		$this->pluginStack[] = array(
			"name"=> $pluginName,
			"args"=> $pluginArgs,
			"body"=> ''
		);
	}

	function inlinePlugin($yytext)
	{
		$pluginName = $this->match('/^\{([a-z]+)/', $yytext);
		$pluginArgs = $this->split(' ', $yytext);
		$pluginArgs = $this->shift($pluginArgs);
		
		return array(
			"name"=> $pluginName,
			"args"=> implode(' ', $pluginArgs),
			"body"=> ''
		);
	}
	
	function npState($npState, $ifTrue, $ifFalse)
	{
		return ($npState == true ? $ifTrue : $ifFalse);
	}
	//end state handlers
	//Wiki Syntax Objects Parsing Start
	function bold($content)
	{
		return "<strong>" . $content . "</strong>";
	}
	
	function box($content)
	{
		return "<div style='border: solid 1px black;'>" . $content . "</div>";
	}
	
	function center($content)
	{
		return "<center>" . $content . "</center>";
	}
	
	function colortext($content)
	{
		$text = $this->split(':', $content);
		$color = $text[0];
		$content = $text[1];
		return "<span style='color: #" . $color . ";'>" . $content . "</span>";
	}
	
	function italics($content)
	{
		return "<i>" . $content . "</i>";
	}
	
	function header1($content)
	{
		return "<h1>" . $content . "</h1>";
	}
	
	function header2($content)
	{
		return "<h2>" . $content . "</h2>";
	}
	
	function header3($content)
	{
		return "<h3>" . $content . "</h3>";
	}
	
	function header4($content)
	{
		return "<h4>" . $content . "</h4>";
	}
	
	function header5($content)
	{
		return "<h5>" . $content . "</h5>";
	}
	
	function header6($content)
	{
		return "<h6>" . $content . "</h6>";
	}
	
	function hr()
	{
		return "<hr />";
	}
	
	function link($content)
	{
		$link = $this->split(':', $content);
		$href = $content;
		
		if ($this->match('/\|/', $content)) {
			$href = $link[0];
			$content = $link[1];
		}
		return "<a href='" . $href . "'>" . $content . "</a>";
	}
	
	function smile($smile)
	{ //this needs more tlc too
		return "<img src='img/smiles/icon_" . $smile . ".gif' alt='" . $smile . "' />";
	}
	
	function strikethrough($content)
	{
		return "<span style='text-decoration: line-through;'>" . $content . "</span>";
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
		return "<table style='width: 100%;'>" . $tableContents . "</table>";
	}
	
	function table_tr($content)
	{
		return "<tr>" . $content . "</tr>";
	}
	
	function table_td($content)
	{
		return "<td>" . $content . "</td>";
	}
	
	function titlebar($content)
	{
		return "<div class='titlebar'>" . $content . "</div>";
	}
	
	function underscore($content)
	{
		return "<u>" . $content . "</u>";
	}
	
	function wikilink($content)
	{
		$wikilink = $this->split('|', $content);
		$href = $content;
		
		if ($this->match('/\|/', $content)) {
			$href = $wikilink[0];
			$content = $wikilink[1];
		}
		return "<a href='" . $href . "'>" . $content . "</a>";
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
	
	function size($array)
	{
		if (empty($array)) $array = array();
		return count($array);
	}
	
	function pop($array)
	{
		if (empty($array)) $array = array();
		array_pop($array);
		return $array;
	}
	
	function push($array, $val)
	{
		if (empty($array)) $array = array();
		array_push($array, $val);
		return $array;
	}
	
	function shift($array)
	{
		if (empty($array)) $array = array();
		array_shift($array);
		return $array;
	}
}
