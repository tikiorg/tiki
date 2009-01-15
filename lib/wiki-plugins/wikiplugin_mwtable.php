<?php

// Written by billferrett@wellbehavedsystems.co.uk
// January 2009
//
// Displays a table using MediaWiki syntax.
//
// As the code base was FANCYTABLE, it seemed fair to keep odd/even styling in.
// If a class is not specified for an element and fancy=false 
//   then class="wikitable" or class="wikicell" is used as appropriate.
//
// Parameters:
//   fancy = true | false (default is false)
//   wiki_classes = true | false (default is true)
//
// Note: wiki classes (wikitable and wikicell) and fancy classes (odd and even)
//         do not work together so fancy takes precedence.
//
// Usage:
//   Optionally, first line (prior to |) contains html attributes for <table>.
//   Optionally, next line can specify a <caption>; line starts |+ followed by
//     optional html attributes that end with a | followed by the caption text.
//   Optionally, column data can be specified next on one or more lines
//     starting with a !.
//   Each column's data starts on a new line with ! or on the same line 
//     preceeded by !!.  Optional html attributes for <col> end with a | 
//     followed by heading text that becomes a <th>.
//   Each row, including the first, starts on a new line with |-, 
//     optionally followed by html attributes for <tr>
//   Each cell starts on a new line with | or on the same line preceeded by ||.
//     Optional html attributes for <td> end with a | followed by the cell text
//     (so data for one cell can have 1 or 2 | characters.  Any other |
//     characters are assumed to be part of the cell text.)
//
// TO DO:
//    Implement row headings.
//
// Example 1:
// {MWTABLE( fancy="true" )} style="width:50%", class="myclass"
// |+My caption
// ! width="30%;" style="background-color:grey;"| Name
// ! Address
// |- style="color: red"
// | style="background-color: pink;" | Bill
// | The little house
// |-
// | Carol || The big house
// {MWTABLE}
//
// Example 2:
// {MWTABLE()}
// |- 
// |Bill
// |The little house
// |-
// |Carol || The big house
// {MWTABLE}

function wikiplugin_mwtable_help() {
	return tra("Displays the data using (sort of) MediaWiki syntax").
                   ":<br />~np~{MWTABLE(fancy=>true|false,wiki_classes=>true|false)}".tra("data")."{MWTABLE}~/np~";
}

function wikiplugin_mwtable_info() {
	return array(
		'name' => tra('MWTable'),
		'documentation' => 'PluginMWTable',
		'description' => tra("Displays a table using MediaWiki syntax"),
		'prefs' => array( 'wikiplugin_mwtable' ),
		'body' => tra('URL'),
		'validate' => 'all',
		'params' => array(
			'fancy' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('fancy'),
				'description' => tra('true|false'),
			),
			'wiki_classes' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('wiki_classes'),
				'description' => tra('true|false'),
			),
		),
	);
}

function wikiplugin_mwtable($data, $params) {
	global $tikilib;

        // set class constants
        $default_class_table = "wikitable";
        $default_class_cell = "wikicell";

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

        // table attributes
        $eol_pos = strpos($data, "\n");
        $attribs = trim(substr($data, 0, $eol_pos));
        if (strpos($attribs, "class=") === false and
            $wiki_classes == true) {
            if (strlen($attribs) > 0) 
                $attribs .= " ";
            $attribs .= "class=\"".$default_class_table."\"";
        }

        if (strlen($attribs)) {
            $wret = "<table ".$attribs.">\n";
        } else {
            $wret = "<table>\n";
        }
        // skip terminating newline
        $remainder = substr($data, $eol_pos + 1);
        
        // caption: prefix = "|+",  suffix = "\n"
        if (substr($remainder, 0, 2)=="|+") {
            $eol_pos = strpos($remainder, "\n");
            $data = substr($remainder, 2, $eol_pos - 3);
            if (strpos($data, "|") > -1) {
                list($attribs, $text) = explode("|", $data, 2);
                $wret .= "<caption ".trim($attribs).">"
                                    .trim($text)."</caption>\n";
            } else {
                $wret .= "<caption>".trim($data)."</caption>\n";
            }
            // skip terminating newline
            $remainder = substr($remainder, $eol_pos + 1);
        }

	// column data: prefix = "!", suffix = "|-"
        if (substr($remainder, 0, 1)=="!") {
            // the end of the column data is the start of the first row
            $eol_pos = strpos($remainder, "|-");
            $data = substr($remainder, 1, $eol_pos - 1);

            // convert "!!" to "\n!" to simplify splitting
            $data = str_replace("!!", "\n!", $data);

            // somewhere to store columns headings
            $ths = array();
            $column_index = 0;
            $th_present = false;
            $columns = explode("!", $data);
            foreach ($columns as $column) {
                // each column
                if (strpos($column, "|")) {
                    list($attribs, $text) = explode("|", $column, 2);
                    $wret .= "<col ".trim($attribs)."/>\n";
                    $ths[$column_index] = trim($text);
                } else {
                    // only one part so use as heading
                    $wret .= "<col/>\n";
                    $ths[$column_index] = trim($column);
                }
                // check if we have a heading
                if (strlen($ths[$column_index]) > 0) {
                    $th_present = true;
                }
                $column_index++;
            }
            
            // output the column heading row
            if ($th_present) {
                $wret .= "<tr>\n";
                for ($counter = 0; $counter < sizeof($ths); $counter++) {

                    $wret .= "<th>".$ths[$counter]."</th>\n";
                }
                $wret .= "</tr>\n";
            }

            // keep terminating "|-"
            $remainder = substr($remainder, $eol_pos);
        }

        // row data; prefix = "|-", suffix = "|-" or end-of-data
        // skip over prefix
	$rows = explode("|-", substr($remainder, 2));

	$row_is_odd = true;
	foreach ($rows as $row) {
            // each row
            // the end of row attributes is the end of the line
            $eol_pos = strpos($row, "\n");
            $attribs = trim(substr($row, 0, $eol_pos - 1));
            if (strlen($attribs) > 0) {
                $wret .= "<tr ".$attribs.">\n";
            } else {
                $wret .= "<tr>\n";
            }

            // extract just the data for the cells
            $row = substr($row, $eol_pos + 2); 

            // the cells
            // convert "||" to "\n|" to simplify splitting
            $row = str_replace("||", "\n|", $row);
            $cells = explode("\n|", $row);
            foreach ($cells as $cell) {
                if (strpos($cell, "|")) {
                    list($attribs, $text) = explode("|", $cell, 2);
                } else {
                    // only one part so use as text
                    $text = $cell;
                    $attribs = "";
                }
                // do we need to add a class? Each cell could be different.
                if (strpos($attribs, "class=") === false) {
                    if ($fancy == true) {
                        if ($row_is_odd) {
                            $class = "class=\"odd\"";
                        } else {
                            $class = "class=\"even\"";
                        }
                    } else {
                        if ($wiki_classes == true) {
                            $class = "class=\"".$default_class_cell."\"";
                        }
                    }
                }
                if (strlen($attribs) > 0) {
                    $attribs .= " ";
                }
                $attribs .= $class;
                // now we can generate a cell
                if (strlen($attribs) > 0) { 
                    $wret .= "<td ".trim($attribs).">".trim($text)."</td>\n";
                } else {
                    $wret .= "<td>".trim($text)."</td>\n";
                }
            }
            // end the row
            $wret .= "</tr>\n";

            // flip the odd-even flag
            $row_is_odd = !$row_is_odd;
	}

	// End the table
	$wret .= "</table>\n";

	return $wret;
}

?>