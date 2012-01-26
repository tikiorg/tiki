<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// wikiplugin_dbreport v1.0
//
// Generates a html report from a database query.
// Jeremy Lee  2009-02-16

// plugin globals
$wikiplugin_dbreport_errors;
$wikiplugin_dbreport_fields;
$wikiplugin_dbreport_fields_allowed;
$wikiplugin_dbreport_record;

class WikipluginDBReportToken
{
	var $type; // key=keyword, fld=field, str=string, var=variable, sty=style, eof=end of file
	var $content;
	var $start;
	var $after;
	var $code;
	function type_name() {
		switch($this->type) {
			case 'key': return 'Keyword';
			case 'txt': return 'Text';
			case 'sty': return 'Style';
			case 'fld': return 'Field';
			case 'var': return 'Variable';
			case 'str': return 'String';
			case 'bra': return 'Brackets';
			case 'eof': return 'End';
			default: return $token->type;
		}
	}
	function WikipluginDBReportToken($content=NULL) {
		$this->content = $content;
	} 
}

class WikipluginDBReportField
{
	var $name;
	var $variable;
	var $break;
	var $index;
	function WikipluginDBReportField($text) {
		global $wikiplugin_dbreport_fields, $wikiplugin_dbreport_fields_allowed;
		$this->name = stripcslashes($text);
		if($text[0]=='$') {
			$this->variable = substr($this->name,1);
			// print("new variable $this->variable ");
		} else {
			// add to the list of parsed fields
			// if($wikiplugin_dbreport_fields_allowed) 
			$wikiplugin_dbreport_fields[] =& $this;
		}
	}
	function text() {
		global $wikiplugin_dbreport_record;
		if(isset($this->index)) {
			// indexed field
			return (string)($wikiplugin_dbreport_record[$this->index]);
		} else if(isset($this->variable)) {
			// PHP variable
			if(isset($GLOBALS[$this->variable])) {
				return (string)$GLOBALS[$this->variable];
			} else if(isset($_SESSION[$this->variable])) {
				return (string)$_SESSION[$this->variable];
			} else if(isset($_REQUEST[$this->variable])) {
				return (string)$_REQUEST[$this->variable];
			}
		} else {
			return "[$this->name]";
		}
	}
	function code() {
		if(isset($this->variable)) {
			return '[$' . addcslashes($this->variable,"\0..\37[]$\\") . ']';
		} else {
			return '[' . addcslashes($this->name,"\0..\37[]$\\") . ']';
		}
	}
	function html() {
		return htmlentities($this->text());
	}
	function uri() {
		return urlencode($this->text());
	}
}

class WikipluginDBReportString
{
	var $literal;
	function WikipluginDBReportString($text) {
		$this->literal = stripcslashes($text);
	}
	function text() {
		return $this->literal;
	}
	function code() {
		return addcslashes($this->literal,"\0..\37[]\\");
	}
	function html() {
		return htmlentities($this->text());
	}
	function uri() {
		return $this->text();
	}
}

class WikipluginDBReportContent
{
	var $elements;  
	function parse_text(&$text) {
		$parse_state = 0;
		$parse_text = '';
		$pos = 0;
		$len = strlen($text);
		while($pos < $len) {
			$char = $text[$pos++];
			switch($parse_state) {
				case 0: // start of next token
					switch($char) {
						case '[':
							$parse_state = 3;
							break;
						case '\\':
							$parse_state = 2;
							$parse_text .= $char;
							break;
						default:
							$parse_state = 1;
							$parse_text .= $char;
					}
					break;
				case 1: // text string
					switch($char) {
						case '[':
							unset($this->elements);
							$this->elements[] = new WikipluginDBReportString($parse_text);
							$parse_text = '';
							$parse_state = 3;
							break;
						case '\\':
							$parse_state = 2;
							$parse_text .= $char;
							break;
						default:
							$parse_text .= $char;
					}
					break;
				case 2: // literal escape
					$parse_text .= $char;
					$parse_state = 1;
					break;
				case 3: // field text
					switch($char) {
						case '[':
							break;
						case ']':
							unset($this->elements);
							$this->elements[] = new WikipluginDBReportField($parse_text);
							$parse_text = '';
							$parse_state = 0;
							break;
						case '\\':
							$parse_state = 4;
							$parse_text .= $char;
							break;
						default:
							$parse_text .= $char;
					}
					break;
				case 4: // field escape
					$parse_text .= $char;
					$parse_state = 3;
					break;
			}
		}
		// hanging text is parsed as a string
		if($parse_state!=0) {
			unset($this->elements);
			$this->elements[] = new WikipluginDBReportString($parse_text);
		}
	}
	function append_field($name) {
		unset($this->elements);
		$this->elements[] = new WikipluginDBReportField($name);
	}
	function append_variable($name) {
		unset($this->elements);
		$this->elements[] = new WikipluginDBReportField('$'.$name);
	}
	function append_string($text) {
		unset($this->elements);
		$this->elements[] = new WikipluginDBReportString($text);
	}
	function append($text) {
		$this->parse_text($text);
	}
	function WikipluginDBReportContent(&$token) {
		switch($token->type) {
			case 'txt':
				$this->parse_text($token->content);
				break;
			case 'fld':
				$this->append_field($token->content);
				break;
			case 'var':
				$this->append_variable($token->content);
				break;
		}
	}
	function text() {
		$result = '';
		if(isset($this->elements)) foreach($this->elements as $element) $result .= $element->text();
		return $result;
	}
	function code() {
		$result = '';
		if(isset($this->elements)) foreach($this->elements as $element) $result .= $element->code();
		return $result;
	}
	function html() {
		$result = '';
		if(isset($this->elements)) foreach($this->elements as $element) $result .= $element->html();
		return $result;
	}
	function uri() {
		$result = '';
		if(isset($this->elements)) foreach($this->elements as $element) $result .= $element->uri();
		return $result;
	}
}

class WikipluginDBReportText extends WikipluginDBReportContent
{
	var $link;
	var $style;
	function code() {
		$result = '"' . addcslashes(parent::code(),'"') . '"';
		if(isset($this->style)) $result .= $this->style->code();
		if(isset($this->link)) $result .= ' '.$this->link->code();
		return $result;
	}
	function html() {
		$html = parent::html();
		if(isset($this->style)) {
			$html = $this->style->html_start() . $html . $this->style->html_end();
		}
		if(isset($this->link)) {
			$html = $this->link->html_start() . $html . $this->link->html_end();
		}
		return $html;
	}
}

class WikipluginDBReportStyle
{
	var $tag;
	var $class;
	var $style;
	function WikipluginDBReportStyle(&$token) {
		if(is_object($token)) {
			if($token->content['class']) {
				$subtoken =& $token->content['class'];
				unset($this->class);
				$this->class = new WikipluginDBReportContent($subtoken);
			}
			if($token->content['style']) {
				$subtoken =& $token->content['style'];
				unset($this->style);
				$this->style = new WikipluginDBReportContent($subtoken);
			}
		} else if(is_string($token)) {
			unset($subtoken);
			$subtoken = new WikipluginDBReportToken($token);
			$subtoken->type = 'txt';
			unset($this->class);
			$this->class = new WikipluginDBReportContent($subtoken);
		}
	}
	function code() {
		$code = ':';
		if(isset($this->class)) {
			$code .= addcslashes($this->class->code(),' ');
		} else if($this->tag!='span') {
			$code .= $this->tag;
		}
		// if(isset($this->class)) $code .= $this->class->code();
		if(isset($this->style)) $code .= "{" . $this->style->code() . "}";
		return $code;
	}
	function attributes() {
		if(isset($this->class)) $html .= ' class="' . $this->class->html() . '"';
		if(isset($this->style)) $html .= ' style="' . $this->style->html() . '"';
		return $html;
	}
	function html_start() {
		if(isset($this->class)) { 
			$class = $this->class->html();
			switch(strtolower($class)) {
				case 'u':
				case 'b':
				case 'i':
				case 'h1':
				case 'h2':
				case 'h3':
				case 'h4':
				case 'h5':
				case 'h6':
					$this->tag = $class;
					$html = '<' . $class;
					break;
				default:
					$this->tag = 'span';				
					$html = '<span class="' . $class . '"';
			}
		} else {
			$this->tag = 'span';				
			$html = '<span';
		}
		if(isset($this->style)) $html .= ' style="' . $this->style->html() . '"';
		$html .= '>';
		return $html;
	}
	function html_end() {
		return '</'.$this->tag.'>';
	}
}

class WikipluginDBReportLink
{
	var $style;
	var $contents;
	function code() {
		$result = '<';
		if(isset($this->contents)) foreach($this->contents as $content) {
			if(is_a($content,'WikipluginDBReportContent')) {
				$result .= '"'.$content->code().'"';
			} else if(is_a($content,'WikipluginDBReportField')) {
				$result .= $content->code();
			}
		}
		if(isset($this->style)) $result .= $this->style->code();
		$result .= '>';
		return $result;
	}
	function uri() {
		if(isset($this->contents)) foreach($this->contents as $content) {
			if(is_a($content,'WikipluginDBReportContent')) {
				$uri .= $content->html();
			} else if(is_a($content,'WikipluginDBReportField')) {
				$uri .= $content->uri();
			}
		}
		return $uri;
	}
	function html_start() {
		$html = '<a href="' . $this->uri() . '"';
		if($this->style) $html .= $this->style->attributes();
		$html .= '>';
		return $html;
	}
	function html_end() {
		return '</a>';
	}
	function html_onclick() {
		return 'onclick="document.location.href=&quot;' . $this->uri() . '&quot;"';
	}
}

class WikipluginDBReportCell
{
	var $link;
	var $style;
	var $colspan;
	var $rowspan;	
	var $contents;
	function code($mode) {
		$result = 'CELL';
		if(isset($this->colspan) && isset($this->rowspan)) {
			if($this->rowspan!=1) $result .= ' ROWSPAN '.$this->rowspan;
			if($this->colspan!=1) $result .= ' COLSPAN '.$this->colspan;
		} else if(isset($this->colspan)) {
			if($this->colspan!=1) if($mode=='ROW') {
				$result .= ' SPAN '.$this->colspan;
			} else {
				$result .= ' COLSPAN '.$this->colspan;
			}
		} else if(isset($this->rowspan)) {
			if($this->rowspan!=1) if($mode=='ROW') {
				$result .= ' ROWSPAN '.$this->rowspan;
			} else {
				$result .= ' SPAN '.$this->rowspan;
			}
		}
		if(isset($this->style)) $result .= ' ' . $this->style->code();
		if(isset($this->link)) $result .= ' ' . $this->link->code();
		if(isset($this->contents)) foreach($this->contents as $content) $result .= ' '.$content->code();
		return $result;
	}
	function html($heading=false) {
		if($heading) {
			$html = '<th';
		} else {
			$html = '<td';
		}
		if(isset($this->style)) $html .= $this->style->attributes();
		if(isset($this->rowspan)) $html .= ' rowspan="' . $this->rowspan . '"';
		if(isset($this->colspan)) $html .= ' colspan="' . $this->colspan . '"';
		if(isset($this->link)) $html .= ' ' . $this->link->html_onclick();
		$html .= '>';
		if(isset($this->contents)) foreach($this->contents as $content) $html .= $content->html();
		$html .= '</td>';
		return $html;
	}
}

class WikipluginDBReportLine
{
	var $link;
	var $styles;
	var $cells;
	function code($indent='',$rtype='ROW',$cellmode='ROW') {
		$result = $indent.$rtype;
		if(isset($this->styles)) foreach($this->styles as $style) $result .= ' '.$style->code();
		if(isset($this->link)) $result .= ' '.$this->link->code();
		$result .= "\n";
		foreach($this->cells as $cell) $result .= $indent.'  '.$cell->code($cellmode)."\n";
		return $result;
	}
	function row_html($data,$style,$heading=false) {
		// set the global report row
		global $wikiplugin_dbreport_record;
		$wikiplugin_dbreport_record = $data;
		// generate HTML
		$html = '<tr';
		if(isset($style)) $html .= $style->attributes();
		if(isset($this->link)) $html .= ' ' . $this->link->html_onclick();
		$html .= '>';
		foreach($this->cells as $cell) $html .= $cell->html($heading);
		$html .= '</tr>' . "\n";
		return $html;
	}
}

class WikipluginDBReportTable
{
	var $style;
	var $headers;
	var $rows;
	var $footers;
	var $style_index;
	function code($indent='') {
		$result = $indent.'TABLE';
		if(isset($this->style)) $result .= ' '.$this->style->code();
		$result .= "\n";
		if(isset($this->headers)) foreach($this->headers as $line) {
			$result .= $line->code($indent.'  ','HEADER');
		}
		if(isset($this->rows)) foreach($this->rows as $line) {
		   $result .= $line->code($indent.'  ','ROW');
		}
		if(isset($this->footers))  foreach($this->footers as $line) {
			$result .= $line->code($indent.'  ','FOOTER');
		}
		return $result;
	}
	function line_row_html($list,$data,$heading=false) {
		$html = '';
		foreach($list as $line) {
			$style = null;
			if(isset($line->styles)) {
				$style_count = count($line->styles);
				$style = $line->styles[$this->style_index % $style_count];
			}
			$html .= $line->row_html($data,$style,$heading);
		}
		return $html;
	}
	function header_row_html($data) {
		$html = '';
		// generate a new table
		if(isset($this->style)) {
			$html .= '<table' . $this->style->attributes() . '>' . "\n";
		} else {
			$html .= '<table>' . "\n";
		}
		// write headers
		$style_index = 0;
		if(isset($this->headers)) $html .= $this->line_row_html($this->headers,$data,true);
		return $html;
	}
	function record_row_html($data) {
		$html = '';
		if(isset($this->rows)) $html .= $this->line_row_html($this->rows,$data);
		$this->style_index++;
		return $html;
	}
	function footer_row_html($data) {
		$html = '';
		// write footers
		$this->style_index = 0;
		if(isset($this->footers)) $html .= $this->line_row_html($this->footers,$data,true);
		// close the table
		$html .= '</table>';
		return $html;
	}
}

class WikipluginDBReportGroup
{
	var $link;
	var $style;
	var $fields;
	var $field_count;
	var $contents;
	function __toString() {
		$result = '';
		foreach($this->contents as $entry) $result .= $entry;
		return $result;
	}
	function code($indent='') {
		$result = $indent.'GROUP';
		if(isset($this->style)) $result .= ' '.$this->style->code();
		if(isset($this->fields)) foreach($this->fields as $field) $result .= ' '.$field->code();
		if(isset($this->link)) $result .= ' '.$this->link->code();
		if(isset($this->contents)) foreach($this->contents as $content) $result .= ' '.$content->code();
		$result .= "\n";
		return $result;
	}
	function check_break(&$row) {
		$ret = false;
		// compare the field values against the break values
		for($i=0; $i<$this->field_count; $i++) {
			$field =& $this->fields[$i];
			$value =& $row[$field->index];
			if($value !== $field->break) {
				$ret = true;
				$field->break =& $value;
			}
		}
		return $ret;
	}
	function start_html($row) {
		global $wikiplugin_dbreport_record;
		$wikiplugin_dbreport_record = $row;
		$html = '';
		// generate a new <div> with the report content at the top
		if(isset($this->style)) {
			$html .= '<div' . $this->style->attributes() . '>' . "\n";
		} else {
			$html .= '<div>' . "\n";
		}
		if(isset($this->contents)) foreach($this->contents as $content) $html.= $content->html(); 
		return $html;
	}
	function end_html(&$row) {
		$html = '';
		// close the <div>
		$html .= '</div>';
		return $html;
	}
}

class WikipluginDBReportParameter extends WikipluginDBReportContent
{
	var $name;
	function code($indent='') {
		$result = $indent.'PARAM';
		// if(isset($this->name)) $result .= ' :'.$this->name;
		if(isset($this->elements)) foreach($this->elements as $element) $result .= ' ' . $element->code();
		$result .= "\n";
		// $result .= ' "' . parent::code() . "\"\n";
		return $result;
	}
}

class WikipluginDBReportFail
{
	var $link;
	var $style;
	var $contents;
	function code($mode) {
		$result = 'FAIL';
		if(isset($this->style)) $result .= ' ' . $this->style->code();
		if(isset($this->link)) $result .= ' ' . $this->link->code();
		if(isset($this->contents)) foreach($this->contents as $content) $result .= ' '.$content->code();
		return $result;
	}
	function html($heading=false) {
		$html = '<div';
		if(isset($this->style)) $html .= $this->style->attributes();
		if(isset($this->link)) $html .= ' ' . $this->link->html_onclick();
		$html .= '>';
		if(isset($this->contents)) foreach($this->contents as $content) $html .= $content->html();
		$html .= '</div>';
		return $html;
	}
}

class WikipluginDBReport
{
	var $sql;
	var $params;
	var $groups;
	var $table;
	var $columns;
	var $fail;
	function code($indent='') {
		// write the report in cannonical form.
		$result = $indent.'SQL {' . $this->sql . '}'."\n";
		if(isset($this->params)) foreach($this->params as $param) $result .= $param->code($indent.'  ');
		if(isset($this->groups)) foreach($this->groups as $group) $result .= $group->code($indent);
		if(isset($this->table)) $result .= $this->table->code($indent);
		if(isset($this->fail)) $result .= $this->fail->code($indent);
		return $result;
	}
}

function wikiplugin_dbreport_parse_error(&$token, $msg) {
	global $wikiplugin_dbreport_errors;
	// find the line relating to the token in the code
	$pos = 0; $line = 0;
	$code =& $token->code;
	$len = strlen($code);
	while($pos<$len) {
		// find the next line break
		$line++;
		$break = strpos($code,"\n",$pos);
		if($break===false) $break = $len;
		// was the token in this line?
		if($token->start < $break) {
			// format the line with the token highlighted
			$err_line = '<i>line '.$line.'</i>: ';
			$err_line .= substr($code,$pos,$token->start-$pos);
			$err_line .= '<span style="font-weight:bold;color:DarkRed;">'.substr($code,$token->start,$token->after-$token->start).'</span>';
			if($token->after<$break) $err_line .= substr($code,$token->after,$break-$token->after);
			$wikiplugin_dbreport_errors[] = $err_line;
			$pos = $len;
		} else {
			// update position and loop
			$pos = $break + 1;
		}
	}
	// add the message to the errors
	$wikiplugin_dbreport_errors[] = $msg;
}

function wikiplugin_dbreport_next_token(&$code, $len, $pos) {
	global $wikiplugin_dbreport_errors, $wikiplugin_dbreport_fields_allowed;
	$whitespace = " \n\r\t\v\f";
	$tokenstop =  " :<>[$\"\n\r\t\v\f";
	// create a token object to return
	unset($token);
	$token = new WikipluginDBReportToken();
	$token->code =& $code;
	// find the next non-whitespace character in the code
	while( ($pos < $len) && (strpos($whitespace,$code[$pos])!==false) ) $pos++;
	if( $pos >= $len ) {
		$token->type = 'eof';
		$token->start = $len - 1;
		$token->after = $pos;
		return $token;
	}
	// what did we find?
	switch($code[$pos]) {
		case '[':
			// field token
			$token->start = $pos;
			// parse to closing ']'
			while( ($pos < $len) && ($code[$pos]!=']') ) {
				if($code[$pos]=='\\') $pos++;
				$pos++;
			}
			if($pos < $len) {
				$token->after = ++$pos;
				$token->type = 'fld';
				$token->content = substr($code, $token->start+1, $pos-$token->start-2);
				return $token;
			} else {
				$token->after = ++$pos;
				$token->type = 'eof';
				wikiplugin_dbreport_parse_error($token, "Unclosed Field. ] expected.");
				return $token;
			}
			break;
		case '{':
			// brackets token
			$token->type = 'bra';
			$token->start = $pos;
			// parse until we find the closing bracket.
			$pos++;
			$state = 0;
			while( ($pos < $len) && ($state<4) ) {
				$c = $code[$pos++];
				switch($state) {
					case 0: // normal content
						switch($c) {
							case '}': $state = 4; break;
							case '`': $state = 1; $token->content .= $c; break;
							case "'": $state = 2; $token->content .= $c; break;
							case '"': $state = 3; $token->content .= $c; break;
							default:  $token->content .= $c;
						}
						break;
					case 1: // backtick-quoted
						switch($c) {
							case '`': $state = 0;
							default:  $token->content .= $c;
						}
						break;
					case 2: // single-quoted 
						switch($c) {
							case '\\': $token->content .= $c . $code[$pos++]; break;
							case '\'': $state = 0;
							default:  $token->content .= $c;
						}
						break;
					case 3: // double-quoted 
						switch($c) {
							case '\\': $token->content .= $c . $code[$pos++]; break;
							case '"': $state = 0;
							default:  $token->content .= $c;
						}
						break;
				}
			}
			$token->after = $pos;
			switch($state) {
				case 0: // unclosed brackets
						wikiplugin_dbreport_parse_error($token, "Unclosed brackets. } expected");
						$token->type = 'eof'; 
						break;
				case 1: // unclosed backtick-quoted content
						wikiplugin_dbreport_parse_error($token, "Unclosed backtick-quoted content in brackets. ` then } expected");
						$token->type = 'eof'; 
						break;
				case 2: // unclosed single-quoted content
						wikiplugin_dbreport_parse_error($token, "Unclosed single-quoted content in brackets. ' then } expected");
						$token->type = 'eof'; 
						break;
				case 3: // unclosed double-quoted content
						wikiplugin_dbreport_parse_error($token, "Unclosed double-quoted content in brackets. \" then } expected");
						$token->type = 'eof';  
						break;
			}
			return $token;
			break;		
		case ':':
			// style token
			$token->type = 'sty';
			$token->start = $pos;
			$token->content = array();
			// create content sub-tokens
			unset($class);
			$class = new WikipluginDBReportToken();
			$class->code =& $code;
			$class->type = 'txt';
			$class->start = $pos;
			unset($style);
			$style = new WikipluginDBReportToken();
			$style->code =& $code;
			$style->type = 'txt';
			// parse until we find the closing space.
			$pos++;
			$state = 0;
			while( ($pos < $len) && ($state<6) ) {
				$c = $code[$pos++];
				if($c=='\\') {
					$c .= $code[$pos++];
					$tc = $c;
				} else {
					$tc = strpos($whitespace,$c)!==false ? ' ': $c;
				}
				switch($state) {
					case 0: // class content
						switch($tc) {
							case '<':
							case '>':
							case '"':
							case ' ': $state = 6; $class->after = $pos; break;
							case '{': $state = 1; $class->after = $pos; $style->start = $pos-1; break;
							case '[': $state = 2;
							default:  $class->content .= $c;
						}
						break;
					case 1: // inline style content
						switch($tc) {
							case '}': $state = 6; $style->after = $pos; break;
							case '[': $state = 3; $style->content .= $c; break;
							case "'": $state = 4; $style->content .= $c; break;
							case '"': $state = 5; $style->content .= $c; break;
							default:  $style->content .= $c;
						}
						break;
					case 2: // class field
						switch($tc) {
							case ']': $state = 0;
							default:  $class->content .= $c;
						}
						break;
					case 3: // inline style field
						switch($tc) {
							case ']': $state = 1;
							default:  $style->content .= $c;
						}
						break;
					case 4: // single-quoted inline style 
						switch($tc) {
							case '\'': $state = 1;
							default:  $style->content .= $c;
						}
						break;
					case 5: // double-quoted inline style 
						switch($tc) {
							case '"': $state = 1;
							default:  $style->content .= $c;
						}
						break;
				}
			}
			switch($state) {
				case 0: // end of file
					$class->after = $pos; 
					break;
				case 1: // inline style content
					$style->after = $pos; 
					wikiplugin_dbreport_parse_error($style, "Unclosed style CSS. } expected");
					$token->type = 'eof'; 
					break;
				case 2: // class field
					$class->after = $pos; 
					wikiplugin_dbreport_parse_error($class, "Unclosed field in style class. ] expected");
					$token->type = 'eof'; 
					break;
				case 3: // inline style field
					$style->after = $pos; 
					wikiplugin_dbreport_parse_error($style, "Unclosed field in style CSS. ] then } expected");
					$token->type = 'eof';  
					break;
				case 4: // single-quoted inline style 
					$style->after = $pos; 
					wikiplugin_dbreport_parse_error($style, "Unclosed single-quoted content in style CSS. ' then } expected");
					$token->type = 'eof'; 
					break;
				case 5: // double-quoted inline style 
					$style->after = $pos; 
					wikiplugin_dbreport_parse_error($style, "Unclosed double-quoted content in style CSS. \" then } expected");
					$token->type = 'eof';  
					break;
			}
			if($class->content) $token->content['class'] = $class;
			if($style->content) $token->content['style'] = $style;
			$token->after = $pos;
			return $token;
			break;
		case '$':
			// variable token
			$token->type = 'var';
			$token->start = $pos;
			$pos++;
			// parse to end of token
			while( ($pos < $len) && (strpos($tokenstop,$code[$pos])===false) )  {
				if($code[$pos]=='\\') $pos++;
				$pos++;
			}
			$token->content = substr($code, $token->start+1, $pos-$token->start-1);
			$token->after = $pos;
			return $token;
			break;
		case '"':
			// string token
			$token->type = 'txt';
			$token->start = $pos;
			$token->content = '';
			// parse until we find the closing quote.
			$pos++;
			while( $pos < $len ) {
				// what is it?
				$c = $code[$pos++];
				switch($c) {
					case '"':
						$token->after = $pos;
						return $token;
						break;
					case '\\':
						$token->content .= $c;
						if( ($pos < $len) ) {
							$c = $code[$pos++];
							$token->content .= $c;
						} else {
							wikiplugin_dbreport_parse_error($token, "Unclosed escaped string. \" expected.");
							$token->type = 'eof';
							return $token;
						}
						break;
					default:
						$token->content .= $c;
						break;
				}
			}
			// didn't find closing quotes
			$token->type = 'txt';
			wikiplugin_dbreport_parse_error($token, "Unterminated string. \" expected.");
			$token->type = 'eof';
			return $token;
			break;
		case '<':
		case '>':
			// link keywords
			$token->type = 'key';
			$token->content = $code[$pos];
			$token->start = $pos;
			$token->after = ++$pos;
			return $token;
			break;
		default:
			// keyword token
			$token->start = $pos;
			// parse to end of token
			while( ($pos < $len) && (strpos($tokenstop,$code[$pos])===false) ) $pos++;
			$token->type = 'key';
			$token->content = substr($code, $token->start, $pos - $token->start);
			$token->after = $pos;
			return $token;
	}
}

function wikiplugin_dbreport_parse(&$code) {
	global $debug, $wikiplugin_dbreport_fields_allowed;
	// code properties
	$len = strlen($code);
	$pos = 0;
    	// FSM state
	$parse_state = 0;
	$parse_link_return = 0;
	$parse_line_return = 0;
	$parse_cell_return = 0;
	$parse_object;
	$parse_text;
	$parse_line;
	$parse_cell;
	$span_mode;
	unset($parse_report);
	$parse_report = new WikipluginDBReport();
	// parse the code
	while(true) {
		// get the next token
		$token = wikiplugin_dbreport_next_token($code, $len, $pos);
		$pos = $token->after;
		// repeat while we have an unconsumed token
		while(isset($token)) {
			$next_token = $token;
			switch($parse_state) {
				case 0: // next keyword
					switch($token->type) {
						case 'eof':
							if(!isset($parse_report->sql)) return wikiplugin_dbreport_parse_error($token, "Unexpected End.");
							return $parse_report;
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case 'SQL':
									$parse_state = 1;	// switch state
									unset($next_token);	// consume the token
									$wikiplugin_dbreport_fields_allowed = false; // no fields in sql
									break;
								case 'PARAM':
									// create the parameter object
									unset($parse_object);
									$parse_object = new WikipluginDBReportParameter($token);
									$parse_report->params[] =& $parse_object;
									$parse_state = 2;	// switch state
									unset($next_token);	// consume the token	
									$wikiplugin_dbreport_fields_allowed = false; // no fields in sql params
									break;
								case 'GROUP':
									// create the group object
									unset($parse_object);
									$parse_object = new WikipluginDBReportGroup();
									$parse_report->groups[] =& $parse_object;
									$parse_state = 3;	// switch state
									unset($next_token);	// consume the token
									$wikiplugin_dbreport_fields_allowed = true; // we can now parse fields						
									break;
								case 'TABLE':
									// create the table object
									unset($parse_object);
									$parse_object = new WikipluginDBReportTable();
									$parse_report->table =& $parse_object;
									$parse_state = 4;	// switch state
									unset($next_token);	// consume the token
									$wikiplugin_dbreport_fields_allowed = true; // we can now parse fields
									break;
								case 'FAIL':
									// create the fail object
									unset($parse_object);
									$parse_object = new WikipluginDBReportFail();
									$parse_report->fail =& $parse_object;
									$parse_state = 10;	// switch state
									unset($next_token);	// consume the token
									$wikiplugin_dbreport_fields_allowed = false; // no fields in fail message
									break;
								default:
									return wikiplugin_dbreport_parse_error($token, "Invalid keyword '$token->content'");
							}
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected ".$token->type." '".$token->content."' at ".$token->start);
					}
					break;
				case 1: // SQL content
					switch($token->type) {
						case 'eof':
							$parse_state = 0; 	// switch state and reparse the token
							break;
						case 'bra':
							$parse_report->sql .= $token->content;
							unset($next_token);	// consume the token
							break;
						case 'txt':
							$parse_report->sql .= stripcslashes($token->content);
							unset($next_token);	// consume the token
							break;
						case 'key':
							$parse_state = 0; 	// switch state and reparse the token
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' after 'SQL'. String expected.");
					}
					break;
				case 2: // PARAM content
					switch($token->type) {
						case 'eof':
							$parse_state = 0; // switch parse state
							break;
						/* case 'sty':
							$parse_object->name = $token->content;
							unset($next_token);	// consume the token
							break; */
						case 'fld':
							$parse_object->append_field($token->content);
							unset($next_token);	// consume the token
							break;
						case 'var':
							$parse_object->append_variable($token->content);
							unset($next_token);	// consume the token
							break;
						case 'txt':
							unset($parse_object->elements);
							$parse_object->elements[] = new WikipluginDBReportText($token);
							unset($next_token);	// consume the token
							break;
						case 'key':
							unset($parse_object);
							$parse_state = 0;	// switch state and reparse the token
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' after 'PARAM'. Name, Field, String or Variable expected.");
					}
					break;
				case 3: // GROUP content
					switch($token->type) {
						case 'eof':
							$parse_state = 0;	// switch state and reparse the token
							break;
						case 'fld':
							unset($parse_object->fields);
							$parse_object->fields[] = new WikipluginDBReportField($token->content);
							$parse_object->field_count++;
							unset($next_token);		// consume the token
							break;
						case 'txt':
						case 'var':
							unset($parse_text);
							$parse_text = new WikipluginDBReportText($token);
							$parse_object->contents[] =& $parse_text;
							$parse_text_return = $parse_state; // return to this state
							$parse_state = 9;	// switch state
							unset($next_token);		// consume the token
							break;
						case 'sty':
							unset($parse_object->style);
							$parse_object->style = new WikipluginDBReportStyle($token);
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case '<':
									unset($parse_link);
									$parse_link = new WikipluginDBReportLink($token);	// create the link object
									$parse_object->link =& $parse_link;
									$parse_link_return = $parse_state; // return to this state
									$parse_state = 5;	// switch state
									unset($next_token);		// consume the token
									break;
								default:
									unset($parse_object); // we are finished parsing the group
									$wikiplugin_dbreport_fields_allowed = false; // we cannot parse fields anymore
									$parse_state = 0;	// switch state and reparse the token
									break;
							}
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' after '<'. Field, String or Style expected.");
					}
					break;
				case 4: // TABLE content
					switch($token->type) {
						case 'eof':
							$parse_state = 0;	// switch state and reparse the token
							break;
						case 'sty':
							unset($parse_object->style);
							$parse_object->style = new WikipluginDBReportStyle($token);
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case 'HEADER':
									unset($parse_line);
									$parse_line = new WikipluginDBReportLine();
									$parse_object->headers[] =& $parse_line;
									$parse_line_return = $parse_state; // return to this state
									$parse_state = 6;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'FOOTER':
									unset($parse_line);
									$parse_line = new WikipluginDBReportLine();
									$parse_object->footers[] =& $parse_line;
									$parse_line_return = $parse_state; // return to this state
									$parse_state = 6;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'ROW':
								case 'ROWS':
									unset($parse_line);
									$parse_line = new WikipluginDBReportLine();
									$parse_object->rows[] =& $parse_line;
									$parse_line_return = $parse_state; // return to this state
									$parse_state = 6;	// switch state
									unset($next_token);		// consume the token
									break;
								default:
									unset($parse_object);	// we are finished parsing the table
									$wikiplugin_dbreport_fields_allowed = false; // we cannot parse fields anymore
									$parse_state = 0;		// switch state and reparse the token
							}
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' after 'TABLE'. HEADER, FOOTER, ROWS, <, or Style expected.");
					}
					break;
				case 5: // Link content
					switch($token->type) {
						case 'eof':
							return wikiplugin_dbreport_parse_error($token, "Unexpected EOF in WikipluginDBReportLink. '>' expected.");
							break;
						case 'var':
						case 'fld':
							unset($parse_link->contents);
							$parse_link->contents[] = new WikipluginDBReportField($token->content);
							unset($next_token);		// consume the token
							break;
						case 'txt':
							unset($parse_link->contents);
							$parse_link->contents[] = new WikipluginDBReportContent($token);
							unset($next_token);		// consume the token
							break;
						/*
						case 'txt':
							$parse_link->append($token->content);
							unset($next_token);		// consume the token
							break;
						case 'var':
							$parse_link->append_variable($token->content);
							unset($next_token);		// consume the token
							break;
						case 'fld':
							$parse_link->append_field($token->content);
							unset($next_token);		// consume the token
							break;
						*/
						case 'sty':
							unset($parse_link->style);
							$parse_link->style = new WikipluginDBReportStyle($token);
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case '<':
									return wikiplugin_dbreport_parse_error($token, "Unexpected '<' in Link. '>' expected.");
								case '>':
									unset($next_token);		// consume the token
									$parse_state = $parse_link_return;	// return to previous state
									break;
								default:
									return wikiplugin_dbreport_parse_error($token, "Unexpected Keyword '$token->content' in Link. '>' expected.");
							}
						default:
							$parse_state = $parse_link_return;	// switch state and reparse the token
					}
					break;
				case 6: // HEADER, FOOTER, ROW content
					switch($token->type) {
						case 'eof':
							$parse_state = $parse_line_return;	// switch state and reparse the token
							break;
						case 'sty':
							unset($parse_link->styles);
							$parse_line->styles[] = new WikipluginDBReportStyle($token);					
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case 'CELL':
									unset($parse_cell);
									$parse_cell = new WikipluginDBReportCell();
									$parse_line->cells[] =& $parse_cell;
									$parse_cell_return = $parse_state; // return to this state
									$parse_state = 7;	// switch state
									unset($next_token);		// consume the token
									break;
								case '<':
									unset($parse_link);
									$parse_link = new WikipluginDBReportLink($token);	// create the link object
									$parse_line->link =& $parse_link;
									$parse_link_return = $parse_state; // return to this state
									$parse_state = 5;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'HEADER':
								case 'ROW':
								case 'FOOTER':
								case 'FAIL':
									$parse_state = $parse_line_return;
									break;
								default:
									return wikiplugin_dbreport_parse_error($token, "Invalid keyword '$token->content' after row. CELL or Link expected.");
							}
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' in row.");
					}
					break;
				case 7: // CELL content
					switch($token->type) {
						case 'eof':
							$parse_state = $parse_cell_return;	// switch state and reparse the token
							break;
						case 'fld':
						case 'var':
						case 'txt':
							unset($parse_text);
							$parse_text = new WikipluginDBReportText($token);
							$parse_cell->contents[] =& $parse_text;
							$parse_text_return = $parse_state; // return to this state
							$parse_state = 9;	// switch state
							unset($next_token);		// consume the token
							break;
						case 'sty':
							unset($parse_cell->style);
							$parse_cell->style = new WikipluginDBReportStyle($token);
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case '<':
									unset($parse_link);
									$parse_link = new WikipluginDBReportLink($token);	// create the link object
									$parse_cell->link =& $parse_link;
									$parse_link_return = $parse_state; // return to this state
									$parse_state = 5;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'SPAN':
									$span_mode = 'COL';
									$parse_state = 8;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'COLSPAN':
									$span_mode = 'COL';
									$parse_state = 8;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'ROWSPAN':
									$span_mode = 'ROW';
									$parse_state = 8;	// switch state
									unset($next_token);		// consume the token
									break;
								case 'CELL':
								case 'HEADER':
								case 'ROW':
								case 'COLUMN':
								case 'FOOTER':
								case 'FAIL':
									$parse_state = $parse_cell_return;	// switch state and reparse the token
									break;
								default:
									return wikiplugin_dbreport_parse_error($token, "Invalid keyword '$token->content' in 'CELL'. Field, String, Style or Link expected.");
							}
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' after 'CELL'.");
					}
					break;
				case 8: // SPAN content
					switch($token->type) {
						case 'key':
							// try to parse the keyword as as number
							$span = (int)$token->content;
							if((string)$span == $token->content) {
								if($span_mode=='ROW') {
									$parse_cell->rowspan = $span;
								} else {
									$parse_cell->colspan = $span;
								}
								unset($next_token);		// consume the token
							}
							$parse_state = 7;	// switch state (and possibly reparse the token)
							break;
						default:
							$parse_state = 7;	// switch state and reparse the token
					}
					break;
				case 9: // Text content
					switch($token->type) {
						case 'sty':
							unset($parse_text->style);
							$parse_text->style = new WikipluginDBReportStyle($token);
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case '<':
									unset($parse_link);
									$parse_link = new WikipluginDBReportLink($token);	// create the link object
									$parse_text->link =& $parse_link;
									$parse_link_return = $parse_state; // return to this state
									$parse_state = 5;	// switch state
									unset($next_token);		// consume the token
									break;
								default:
									$parse_state = $parse_text_return;	// return to the previous state
									break;
							}
							break;
						default:
							$parse_state = $parse_text_return;	// return to the previous state
							break;
					}
					break;
				case 10: // Fail content
					switch($token->type) {
						case 'eof':
							$parse_state = 0;	// switch state and reparse the token
							break;
						case 'var':
						case 'txt':
							unset($parse_text);
							$parse_text = new WikipluginDBReportText($token);
							$parse_object->contents[] =& $parse_text;
							$parse_text_return = $parse_state; // return to this state
							$parse_state = 9;		// switch state
							unset($next_token);		// consume the token
							break;
						case 'sty':
							unset($parse_object->style);
							$parse_object->style = new WikipluginDBReportStyle($token);
							unset($next_token);		// consume the token
							break;
						case 'key':
							switch(strtoupper($token->content)) {
								case '<':
									unset($parse_link);
									$parse_link = new WikipluginDBReportLink($token);	// create the link object
									$parse_object->link =& $parse_link;
									$parse_link_return = $parse_state; // return to this state
									$parse_state = 5;	// switch state
									unset($next_token);		// consume the token
									break;
								default:
									unset($parse_object); // we are finished parsing the fail
									$parse_state = 0;	// switch state and reparse the token
									break;
							}
							break;
						default:
							return wikiplugin_dbreport_parse_error($token, "Unexpected " . $token->type_name() . " '$token->content' after 'FAIL'.");
					}
					break;
				default:
					$parse_state = 0;
			}
			$token = $next_token;
		}
	}
}

function wikiplugin_dbreport_error_box($error) {
	$return = '~np~<table style="border-width:1px; border-style:dashed; border-color:red; background:#FFE0E0;"><tr><td>';
	switch(gettype($error)) {
		case 'array':
			foreach($error as $entry) $return .= $entry.'<br/>';
			break;
		case 'string':
		case 'object':
			$return .= (string)$error;
			break;
		default:
			$return .= gettype($error).' ERROR!';
	}
	$return .= '</td></tr></table>~/np~';
	return $return;
}

function wikiplugin_dbreport_message_box($msg) {
	$return = '<table style="border-width:1px; border-style:dashed; border-color:silver; background:#E0E0FF;"><tr><td>';
	switch(gettype($error)) {
		case 'array':
			foreach($msg as $entry) $return .= $entry.'<br/>';
			break;
		default:
			$return .= (string)$msg;
	}
	$return .= '</td></tr></table>';
	return $return;
}

function wikiplugin_dbreport_help() {
	return tra("Run a database report").":<br />~np~{DBREPORT(dsn=>dsnname | db=>dbname, wiki=0|1, debug=>0|1)}".tra("report definition")."{DBREPORT}~/np~";
}


function wikiplugin_dbreport_info() {
	return array(
		'name' => tra('DB Report'),
		'documentation' => 'PluginDBReport',
		'description' => tra('Query a database and display results (only works with ADOdb, does not work with PDO)'),
		'prefs' => array('wikiplugin_dbreport'),
		'body' => tra('report definition'),
		'validate' => 'all',
		'icon' => 'pics/icons/database_table.png',
		'params' => array(
			'dsn' => array(
				'required' => false,
				'name' => tra('Full DSN'),
				'description' => tra('A full DSN (Data Source Name) connection string. eg: mysql://user:pass@server/database'),
				'default' => '',
			),
			'db' => array(
				'required' => false,
				'name' => tra('Wiki DSN Name'),
				'description' => tra('The name of a DSN connection defined by the Wiki administrator.'),
				'default' => '',
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Syntax'),
				'description' => tra('Parse wiki syntax within the report (not parsed by default)'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'debug' => array(
				'required' => false,
				'name' => tra('Debug'),
				'description' => tra('Display the parsed report definition (not displayed by default)'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
		),
	);
}

	
function wikiplugin_dbreport($data, $params) {
	// TikiWiki globals
	global $tikilib, $user, $group, $page, $prefs;
	global $wikiplugin_dbreport_errors, $wikiplugin_dbreport_fields;
	// wikiplugin_dbreport globals
	global $wikiplugin_dbreport_errors;
	global $wikiplugin_dbreport_fields;
	global $wikiplugin_dbreport_fields_allowed;
	global $wikiplugin_dbreport_record;
	// initialize globals
	$wikiplugin_dbreport_errors = array();
	$wikiplugin_dbreport_fields = array();
	$wikiplugin_dbreport_fields_allowed = false;
	$wikiplugin_dbreport_record = null;
	// extract parameters
	extract ($params,EXTR_SKIP);
	// debug plugin input
	if($debug_input) {
		$msg = $_REQUEST['preview'] . ' ' . $_SESSION['s_prefs']['tiki_release'] . '<br/>';
		$msg .= '~np~<pre>'.htmlspecialchars($data).'</pre>~/np~';
		$ret .= wikiplugin_dbreport_message_box($msg);
	}
	// we need a dsn or db parameter
	if (!isset($dsn) && !isset($db)) {
		return tra('Missing db or dsn parameter');
	}
	// parse the report definition
	$parse_fix = ($_REQUEST['preview']) && ($_SESSION['s_prefs']['tiki_release']=='2.2');
	if($parse_fix) {
		$report =& wikiplugin_dbreport_parse($data);
	} else {
		$report =& wikiplugin_dbreport_parse(html_entity_decode($data));
	}
	// were there errors?
	if($wikiplugin_dbreport_errors) {
		$ret .= wikiplugin_dbreport_error_box($wikiplugin_dbreport_errors);
		return $ret;
	}
	// create the bind variables array
	$bindvars = array();
	if(isset($report->params)) foreach($report->params as $param) {
		if(isset($param->name)) {
			$bindvars[$param->name] = $param->text();
		} else {
			$bindvars[] = $param->text();
		}
	}
	// translate db name into dsn
	if (isset($db)) {
		$perms = Perms::get( array( 'type' => 'dsn', 'object' => $db ) );
		if ( ! $perms->dsn_query ) {
			return tra('You do not have permission to use this feature');
		}
		// retrieve the dsn string
		$dsn = $tikilib->get_dsn_by_name($db);
	}
	// open the database
	if (isset($dsn)) {
		// open database dsn connection
		require_once ('lib/adodb/adodb.inc.php');
		$ado = ADONewConnection($dsn);
		if (!$ado) {
			$ret .= wikiplugin_dbreport_error_box($ado->ErrorMsg());
			return $ret;
		} else {
			// execute sql query
			$ado->SetFetchMode(ADODB_FETCH_NUM);
			$query =& $ado->Execute($report->sql, $bindvars); 
			$field_count = $query->FieldCount();
			$fetchfield = 'FetchField';
			if (!$query) {
				$ret .= wikiplugin_dbreport_error_box($ado->ErrorMsg());
				return $ret;
			}
		}
	} else {
		return (tra('No DSN connection string found!'));
	}
	// create an array of field names and their index	
	$field_index = array();
//	$field_count = $query->FieldCount();
	for($index = 0; $index<$field_count; $index++) {
		$column =& $query->$fetchfield($index);
		$field_index[$column->name] = $index;
	}
	// go through the parsed fields and assign indexes
	foreach($wikiplugin_dbreport_fields as $key => $value) {
		$parse_field =& $wikiplugin_dbreport_fields[$key];
		$index = $field_index[$parse_field->name];
		if(isset($index)) {
			$parse_field->index = $index;
		} else {
			// not a valid field. log the message.
			$ret .= wikiplugin_dbreport_error_box("The Field '$parse_field->name' was not returned by the SQL query.");
			return $ret;
		}
	}
	// does the report have a table definition?
	if(!isset($report->table)) {
		// create a default definition from the data
		$report->table = new WikipluginDBReportTable();
		$style = 'sortable';
		$report->table->style = new WikipluginDBReportStyle($style);
		$header = new WikipluginDBReportLine();
		$style = 'heading';
		$header->styles[] = new WikipluginDBReportStyle($style);
		$report->table->headers[] =& $header;
		$row = new WikipluginDBReportLine();
		$style = 'even';
		$row->styles[] = new WikipluginDBReportStyle($style);
		$style = 'odd';
		$row->styles[] = new WikipluginDBReportStyle($style);
		$report->table->rows[] =& $row;
		// fill in the cells
		$field_count = $query->FieldCount();
		for($index = 0; $index<$field_count; $index++) {
			// get the query field
			$column =& $query->FetchField($index);
			// create the header cell
			unset($text);
			$text = new WikipluginDBReportText(new WikipluginDBReportToken());
			$text->append_string($column->name);
			unset($cell);
			$cell = new WikipluginDBReportCell();
			// $style = 'heading';
			// $cell->style = new WikipluginDBReportStyle($style);
			$cell->contents[] =& $text;
			$header->cells[] =& $cell; 
			// create the rows cell
			unset($cell);
			$cell = new WikipluginDBReportCell();
			unset($field);
			$field = new WikipluginDBReportField($column->name);
			$field->index = $index;
			$cell->contents[] =& $field;
			$row->cells[] =& $cell; 
		}
	}
	// are we debugging?
	if($debug) $ret .= wikiplugin_dbreport_message_box("~np~<pre>".htmlspecialchars($report->code())."</pre>~/np~");
	// generate the report
	if(!$wiki) $ret .= '~np~';
	if(!$query->EOF) {
		// get the first row
		$current_row = $query->FetchRow();
		// start the group breaks
		if (isset($report->groups)) {
			foreach($report->groups as $group) {
				$group->check_break($current_row);
				$ret .= $group->start_html($current_row);
			}
		}
		// first row is always considered 'after a break'
		$breaking = true;
		// go through the rows
		while ($current_row) {
			// do we generate a table header?
			if($breaking) $ret .= $report->table->header_row_html($current_row);
			// write the table row
			$ret .= $report->table->record_row_html($current_row);
			// get the next row
			if($query->EOF) {
				unset($next_row);
				$breaking = true;
			} else {
				$next_row = $query->FetchRow();
				$breaking = false;
			}
			// check group breaks
			if (isset($report->groups)) {
				$break_end = '';
				$break_start = '';
				foreach($report->groups as $group) {
					if(isset($next_row)) $breaking = ($group->check_break($next_row) || $breaking);
					if($breaking) {
						$break_end = $group->end_html($current_row) . $break_end;
						if(isset($next_row)) $break_start .= $group->start_html($next_row);
					}
				}
			}
			if($breaking) {
				$ret .= $report->table->footer_row_html($current_row);
				$ret .= $break_end;
				$ret .= $break_start;
			}
			// move to the next row
			$current_row = $next_row;
		}
	} else {
		// no records returned. output the fail message
		if($report->fail) $ret .= $report->fail->html();
	}
	if(!$wiki) $ret .= '~/np~';
	// close the database connection
	$query->Close();
	$ado->Close();
	// return the result
	return $ret;
}
