<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_mwtable_info() {
	return array(
		'name' => tra('MediaWiki Table'),
		'documentation' => 'PluginMWTable',
		'description' => tra('Display a table using MediaWiki syntax (experimental - may change in future versions)'),
		'prefs' => array( 'wikiplugin_mwtable' ),
		'body' => tra('URL'),
		'validate' => 'all',
		'icon' => 'pics/icons/table.png',
		'params' => array(
			'fancy' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Fancy'),
				'description' => tra('Set to true to apply additional formatting to the table (header style, odd/even rows, etc.)'),
				'default' => 'false',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('True'), 'value' => 'true'), 
					array('text' => tra('False'), 'value' => 'false')
				)
			),
			'wiki_classes' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Wiki Classes'),
				'description' => tra('Determines whether wiki style classes will be used for the table and cells (used by default)'),
				'default' => 'true',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('True'), 'value' => 'true'), 
					array('text' => tra('False'), 'value' => 'false')
				)
			)
		)
	);
}

function wikiplugin_mwtable($data, $params) {
	global $tikilib;

	// Parse the parameters
	extract ($params,EXTR_SKIP);
	if (isset($fancy) and $fancy=="true") {
            $fancy = true;
        } else {
            $fancy = false;
        }
	if (isset($wiki_classes) and $wiki_classes=="false") {
            $wiki_classes = false;
        } else {
            $wiki_classes = true;
        }

        // set class constants
        $default_class_table = "normal";
        $default_class_heading = "";
        $default_class_td_odd = "";
        $default_class_td_odd = "";
        if ($fancy) {
            $default_class_table = "normal";
            $default_class_heading = "heading";
            $default_class_td_odd = "odd";
            $default_class_td_even = "even";
        } else if ($wiki_classes) {
            $default_class_table = "wikitable";
            $default_class_td_odd = "wikicell";
            $default_class_td_even = "wikicell";
        }

        // soe = start-of-element; eoe = end-of-element
        // set the start position
        $soe = 0;

        // <table>: prefix = start-of-string, suffix = "\n"
        $suffix = "\n";
        $eoe = strpos($data, $suffix);

        // we only have attributes
        $element = substr($data, $soe, $eoe - strlen($suffix));
        $attributes = _get_attributes($element);

        _check_class_attribute($attributes,$default_class_table);

        $wret .= _output_tag_with_attributes("table",$attributes,true);

        // move soe - skip terminating suffix
        $soe = $eoe + strlen($suffix);
        // <table>: end (of start tag)

        // <caption>: prefix = "|+",  suffix = "\n"
        $prefix = "|+";
        $suffix = "\n";
        if (substr($data, $soe, strlen($prefix))==$prefix) {
            $eoe = strpos($data, $suffix, $soe);
            $element = substr($data, $soe + strlen($prefix), 
                              $eoe - $soe - strlen($prefix) - strlen($suffix));
            if (strpos($element, "|")) {
                // attributes present
                list($attribs, $text) = explode("|", $element, 2);
                $attributes = _get_attributes($attribs);
                $wret .= _output_tag_with_attributes("caption",$attributes,true);
                $wret .= trim($text);
            } else {
                $wret .= "<caption>".trim($element);
            }
            $wret .= "</caption>\n";
            // move soe - skip terminating suffix
            $soe = $eoe + strlen($suffix);
        }
        // <caption>: end

	// <col>: prefix = "?", suffix = "!" or "|-"
        $prefix = "?";
        $suffix = "!";
        if (substr($data, $soe, strlen($prefix))==$prefix) {
            // the end of the column data is the start of the headings
            // or the start of the rows
            $pos_headings = strpos($data, "!", $soe);
            $pos_rows = strpos($data, "|-", $soe);
            if ($pos_headings) {
                //assume if headings present they precede any row
                $eoe = $pos_headings;
            } else {
                // assume element delimited by first row
                $suffix = "|-";
                $eoe = $pos_rows;
            }
            $element = substr($data, $soe + strlen($prefix),
                              $eoe - $soe - strlen($prefix) - strlen($suffix));

            // convert "??" to "\n?" to simplify splitting
            $element = str_replace("??", "\n?", $element);

            $columns = explode("?", $element);
            foreach ($columns as $column) {
                // we only have attributes
                $attributes = _get_attributes($column);
                $wret .= _output_tag_with_attributes("col",$attributes,true,true);
            }
            
            // move soe - keep terminating string
            $soe = $eoe;
        }
        // <col>: end

	// <th>: prefix = "!", suffix = "|-"
        $prefix = "!";
        $suffix = "|-";
        if (substr($data, $soe, strlen($prefix))==$prefix) {
            // the end of the heading data is the start of the first row
            $eoe = strpos($data, "|-", $soe);
            $element = substr($data, $soe + strlen($prefix),
                              $eoe - $soe - strlen($prefix) - strlen($suffix));

            // convert "!!" to "\n!" to simplify splitting
            $element = str_replace("!!", "\n!", $element);

            $columns = explode("!", $element);
            $wret .= "<tr>\n";
            foreach ($columns as $column) {
                // each column can have attributes and/or text
                if (strpos($column, "|")) {
                    list($attribs, $text) = explode("|", $column, 2);
                    $attributes = _get_attributes($attribs);
                    _check_class_attribute($attributes,$default_class_headings);        
                    $wret .= _output_tag_with_attributes("th",$attributes);
                    $wret .= trim($text);
                } else {
                    // only one part so use as heading
                    $wret .= "<th>".trim($column);
                }
                $wret .= "</th>\n";
            }
            $wret .= "</tr>\n";
            
            // move soe - keep terminating "|-"
            $soe = $eoe;
        }
        // <th>: end

        // <tr>: prefix = "|-", suffix = "|-" or end-of-data
        // skip over prefix for first row
        $prefix = "|-";
	$rows = explode($prefix, substr($data, $soe + strlen($prefix)));

	$row_is_odd = true;
	foreach ($rows as $row) {
            // each row
            if ($row_is_odd) {
                $default_class_td = $default_class_td_odd;
            } else {
                $default_class_td = $default_class_td_even;
            } 
            $row_is_odd = !$row_is_odd;

            // the end of row attributes is the end of the line/start of cell
            $suffix = "\n|";
            $eoe = strpos($row, $suffix);
            $attribs = substr($row, 0, $eoe - strlen($suffix));
            $attributes = _get_attributes($attribs);
            $wret .= _output_tag_with_attributes("tr",$attributes,true);

            // extract just the data for the cells - skip prefix of first cell
            $soe = $eoe + strlen($suffix);
            $row_cells = substr($row, $soe); 

            // the cells
            $prefix = "\n|";

            // convert "||" to "\n|" to simplify splitting
            $row_cells = str_replace("||", $prefix, $row_cells);

            // get cells - skip over prefix of first cell
            $cells = explode($prefix, $row_cells);
            foreach ($cells as $cell) {
                if (strpos($cell, "|")) {
                    list($attribs, $text) = explode("|", $cell, 2);
                    $attributes = _get_attributes($attribs);
                } else {
                    // only one part so use as text
                    $attributes = array();
                    $text = $cell;
                }
                _check_class_attribute($attributes,$default_class_td);        
                $wret .= _output_tag_with_attributes("td",$attributes);
                $wret .= trim($text);

                // end of cell
                $wret .= "</td>\n";
            }
            // end the row
            $wret .= "</tr>\n";
	}

	// End the table
	$wret .= "</table>\n";

	return $wret;
}

// We need a function to parse attributes as wiki syntax rules do 
// funny things to the data.
//   % needs to be enclosed in double-quotes to not be a dynamic variable
//   double-quotes have become &quot; which is no good for an html attribute
//
// Return an associative array where value has had any delimiters removed.

function _get_attributes($string) {
        $attributes = array();
        $cur_pos = 0;

        while ($cur_pos < strlen($string)) {
          // identify key
          if (preg_match("/\s*(\w+)=/",$string,$matches,PREG_OFFSET_CAPTURE,$cur_pos)) {
            $cur_pos += strlen($matches[0][0]);   // skip whole pattern
            $key = $matches[1][0];                // just store key
            // identify value:  
            // use # as start/end character inclusion of / in value
            if (preg_match("#([(&quot;)\"'\w:;%-/\.]+)(?=\s|\Z)#",$string,$matches,PREG_OFFSET_CAPTURE,$cur_pos)) {
              $cur_pos += strlen($matches[0][0]); // skip whole pattern
              $value = $matches[1][0];            // just store value
              // remove delimiters (quote or double-quote if present)
              $value = str_replace("'", "", $value);
              $value = str_replace("&quot;", "", $value);
            } else {
              $value = substr($string,$cur_pos);
              $cur_pos = strlen($string);
            }
            $attributes[$key] = $value;
          } else {
            $cur_pos = strlen($string);
          } 
        }
        return $attributes;
}

// If terminate_tag==False return in the form <tag attribute="value">
// If terminate_tag==True return in the form <tag attribute="value"/>
// The latter is only applicable to a few tags like <col>.
function _output_tag_with_attributes($tag,$attributes,$newline = False,
                                     $terminate_tag = False) {
        $output = "<".$tag;
        if (count($attributes)) {
            foreach($attributes as $key=>$value) {
              $output .= " ".$key."=\"".$value."\"";
            }
        }
        if ($terminate_tag) $output .= "/";
        $output .= ">";
        if ($newline) $output .= "\n";
        return $output;
}

// Check the class element.
// if $add==True add default class to any existing class else replace
function _check_class_attribute(&$attributes,$default_class = "",$add = True) {
        // if no default class then nothing to do
        if (strlen($default_class) == 0) 
            return;
        $class = $default_class;
        if ($add and (array_key_exists("class",$attributes))) 
            $class .= " ".$attributes["class"];
        $attributes["class"] = $class;
}
