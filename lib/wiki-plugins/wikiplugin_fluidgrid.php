<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fluidgrid_info()
{
	return array(
		'name' => tra('Fluid Grid'),
		'documentation' => 'PluginFluidGrid',
		'description' => tra('Arrange content into rows and columns using the bootstrap fluid grid'),
		'prefs' => array( 'wikiplugin_fluidgrid' ),
		'body' => tra('Text'),
		'filter' => 'wikicontent',
		'iconname' => 'table',
		'introduced' => 1,
		'tags' => array( 'basic' ),
		'params' => array(
			'joincols' => array(
				'required' => false,
				'name' => tra('Join Columns'),
				'description' => tra('Generate the colspan attribute if columns are missing'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'colsize' => array(
				'required' => false,
				'name' => tra('Column Sizes'),
				'description' => tra('Specify all column widths in units which add up to 12'),
				'since' => '1',
				'seprator' => '|',
				'filter' => 'text',
				'default' => '',
			),
			'first' => array(
				'required' => false,
				'name' => tra('First'),
				'description' => tra('Cells specified are ordered first left to right across rows (default) or top to
					bottom down columns'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'line',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Column'), 'value' => 'col'), 
					array('text' => tra('Line'), 'value' => 'line')
				)
			),
			'customclass' => array(
				'required' => false,
				'name' => tra('Custom Class'),
				'description' => tra('Add a class to customize the design'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

//
// The plugin function starts by removing a list of other plugins, nested
// inside this one. This function patches them back in.
// 
function wikiplugin_fluidgrid_rollback($data, $hashes)
{
	foreach ($hashes as $hash=>$match) {
		$data = str_replace($hash, $match, $data);
	}
	return $data;
}

/*
 * \note This plugin should carefuly change text it have to parse
 *       because some of wiki syntaxes are sensitive for
 *       start of new line ('\n' character - e.g. lists and headers)... such
 *       user lines must stay with the same layout when applying
 *       this plugin to render them properly after...
 *		$data = the preparsed data (plugin, code, np.... already parsed)
 *		$pos is the position in the object where the non-parsed data begins
 */
function wikiplugin_fluidgrid($data, $params, $pos)
{
	//
    // The following function uses a regular expression in the form
	// "/pattern/ismU"
    // where / is used as a delimiter and ismU are pattern modifiers
    // i = case insensitive
    // s = dot matches anything (including new line)
	// m = multiline 
    // U = Ungreedy 
    //
    // The regular expression matches a list of specific plugins. The following
    // loop replaces the plugin with a hash, so that it is excluded from the 
    // processing.
    //
    // Question:
    // Ungreedy matching prevents us spanning multiple instances of a plugin,
    // e.g. {SPIIT()}...{SPLIT}...{SPIIT()}...{SPLIT}
    // Will it handle a second level of nested plugins of the same type, 
    // e.g. {SPIIT()}...{SPLIT()}...{SPIIT}...{SPLIT}
    //
    global $tikilib, $tiki_p_admin_wiki, $tiki_p_admin, $section;
	global $replacement;
	preg_match_all('/{(FLUIDGRID|SPLIT|CODE|HTML|FADE|JQ|JS|MOUSEOVER|VERSIONS).+{\1}/ismU', $data, $matches);
	$hashes = array();
	foreach ($matches[0] as $match) {
		if (empty($match)) continue;
		$hash = md5($match);
		$hashes[$hash] = $match;
		$data = str_replace($match, $hash, $data);
	}

	// Remove first <ENTER> if exists...
	// it may be here if present after {FLUIDGRID()} in original text
	if (substr($data, 0, 2) == "\r\n")
		$data2 = substr($data, 2);
	else
		$data2 = $data;
	
	extract($params, EXTR_SKIP);
	$joincols  = (!isset($joincols)  || $joincols  == 'y' || $joincols  == 1 ? true : false);
	// Split data by rows and cells

	$sections = preg_split("/@@@+/", $data2);
	$rows = array();
	$maxcols = 0;
	foreach ($sections as $i) {
		// split by --- but not by ----
		//	$rows[] = preg_split("/([^\-]---[^\-]|^---[^\-]|[^\-]---$|^---$)+/", $i);
		//	not to eat the character close to - and to split on --- and not ----
		$rows[] = preg_split("/(?<!-)---(?!-)/", $i);
		$maxcols = max($maxcols, count(end($rows)));
	}

	// Are there any sections present?
	// Do not touch anything if not... don't even generate <table>
	if (count($rows) <= 1 && count($rows[0]) <= 1)
	   return wikiplugin_fluidgrid_rollback($data, $hashes);

	//
    // The "first" parameter indicates whether the content is listed in columns
    // or in rows (aka. lines).
    //
    // I doubt whether columm mode is very useful, but I intend to support it.
    //
    // The original SPLIT plugin generates a normal table with rows and cells
    // for line mode, but handles column mode with a single row. The separate
    // rows in each column are defined with divs. This is imho Not good enough.
    //
    // Because I think it is an exotic case, I will handle column mode by
    // flipping the matrices before generating the table. This is probably
    // not very efficient, but keeps the code fairly readable. 
    //
	if ( isset($first) && $first == 'col' ) 
    {
      $cols    = array() ;
      $maxrows = count($rows) ;
      
      for ( $i=0 ; $i<$maxcols ; $i++ )
      {
        $cols[] = array() ;
      }
      foreach ($rows as $r) 
      {
        for ( $i=0 ; $i<$maxcols ; $i++ )
        {
          if ( $i < count($r) )
            $cols[$i][] = $r[$i] ;
          else  
            $cols[$i][] = '' ;
        }
      }
      
      $rows = $cols ;
      $maxcols = $maxrows ;
    }

    // Handle the column widths.
    // The colsize parameter can be used to specify column widths. If used
    // then the values should add up to 12, e.g. 6|2|2|2
    
    if ( isset($colsize) ) 
        $tdsize = explode("|", $colsize);
    else    
        $tdsize = array() ;
        
    // Total size of columns with specified size
    $tdtotal  = 0 ;
    
    // Count of columns without specified size
    $tdnosize = 0 ;
    
    for ( $i=0 ; $i<$maxcols ; $i++ ) 
    {
      // I want something like 6||2 to behave in a reasonable manner
      // (although I'm not sure it is really useful) so check for 
      // empty strings.
      if ( isset ( $tdsize[$i] ) && ( trim ( $tdsize[$i] ) != '' ) ) 
      {
        $tdsize[$i] = trim ( $tdsize[$i] ) ;
        $tdtotal    += $tdsize[$i] ;
      }
      else
      {
        $tdsize[$i] = 0 ;
        $tdnosize++ ;
      }
    }
    
    // Share the remaining space out among the unsized columns
    $remaining = 12 - $tdtotal ;
    $share     = ceil ( $remaining / $tdnosize ) ;
    
    for ( $i=0 ; $i<$maxcols ; $i++ ) 
    {
      if ( $tdsize[$i] == 0 ) 
      {
        if ( $remaining > $share )
        {
          $tdsize[$i] = $share ;
          $remaining -= $share ;
        }
        else
        {
          $tdsize[$i] = $remaining ;
          $remaining  = 0 ;
        }   
      }
    }
    
	//$result = "<div class='container-fluid" . ( !empty($customclass) ? " $customclass" : "") . "'>" ;
	$result = "<div" . ( !empty($customclass) ? " class='$customclass'" : "") . ">" ;

	foreach ($rows as $r) 
    {
	  $result .= "<div class='row'>" ;
      
      $idx = 0 ;
      foreach ($r as $i) 
      {
        // Insert "\n" at data begin (so start-of-line-sensitive syntaxes will be parsed OK)
        $result .= "<div class='col-sm-" . $tdsize[$idx] . "'>\n" . trim($i) . "</div>" ;

        $idx++;
      }
      
      $result .= "</div>";
	}

	// Close HTML table (no \n at end!)
	$result .= "</div>";

	return wikiplugin_fluidgrid_rollback($result, $hashes);
}

