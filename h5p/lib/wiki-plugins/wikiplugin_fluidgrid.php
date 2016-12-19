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
                'description' => tra('Merge empty cells into the cell to their left'),
                'since' => '1',
                'filter' => 'alpha',
                'default' => 'y',
                'options' => array(
                    array('text' => '', 'value' => ''), 
                    array('text' => tra('Yes'), 'value' => 'y'), 
                    array('text' => tra('No'), 'value' => 'n')
                )
            ),
            'devicesize' => array(
                'required' => false,
                'name' => tra('Device size'),
                'description' => tra('Specify the device size below which the cells will be stacked vertically'),
                'since' => '1',
                'filter' => 'alpha',
                'default' => 'sm',
                'options' => array(
                    array('text' => '',                 'value' => ''), 
                    array('text' => tra('Small'),       'value' => 'sm'), 
                    array('text' => tra('Medium'),      'value' => 'md'), 
                    array('text' => tra('Large'),       'value' => 'lg'), 
                    array('text' => tra('Extra Large'), 'value' => 'xl')
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
                'description' => tra('Cells specified are ordered first left to right across rows (default) or top to bottom down columns'),
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
 *      $data = the preparsed data (plugin, code, np.... already parsed)
 *      $pos is the position in the object where the non-parsed data begins
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
    
    // Check the device size parameter which must be one of 'sm', 'md', 'lg' or 'xl'
    if ( !isset($devicesize) || !( ( $devicesize == 'sm' ) || ( $devicesize == 'md' ) || ( $devicesize == 'lg' ) || ( $devicesize == 'xl' ) ) )
    {
      $devicesize = 'sm' ;     
    }
    
    // Split data by rows and cells
    $sections = preg_split("/@@@+/", $data2);
    $rows = array();
    $maxcols = 0;
    foreach ($sections as $i) {
        // split by --- but not by ----
        //  $rows[] = preg_split("/([^\-]---[^\-]|^---[^\-]|[^\-]---$|^---$)+/", $i);
        //  not to eat the character close to - and to split on --- and not ----
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

    // The bootstrap fluid grid can handle a maximum of 12 colums.
    // Check this AFTER flipping the axis for column mode.
    if ( $maxcols > 12 )
      return ( "<b>Fluid Grid can have a maximum of 12 columns</b><br/>") ;
      
    // Handle the column widths.
    //
    // There are several cases:
    // (not in the order in which they must be handled)
    //
    // (1) Colsize is not present
    //     - Share out the space as evenly as possible
    //
    // (2) Colsize is present
    //     It specifies the size of all columns
    //     The total size is <= 12
    //     - Assign exactly the specified sizes
    //  
    // (3) Colsize is present
    //     It does not specifiy the size of all columns
    //     The total size plus the number of unsized columns is <= 12
    //     - Assign the specified sizes and share out the remaining
    //       units among the unsized columns
    //  
    // The remaining cases are for some degree of compatibility with the 
    // SPLIT plugin.  
    //  
    // (4) Colsize is present
    //     It specifies the size of all columns in PIXELS
    //     The total size is > 12
    //     - Use the size as an approximate weighting
    //  
    // (5) Colsize is present
    //     It specifies the size of some but not all columns in PIXELS
    //     The total size is > 12
    //     - Use the size as an approximate weighting, with a minimum size of 1.
    //  
    // (6) Colsize is present
    //     All columns are specified in PERCENT.
    //     - Use the size as an approximate weighting, relative to 100, with a 
    //       minimum size of 1.
    //     - The total can be less than 12, e.g. two columns with 25%|25%
    //       should translate to 3|3 and not 6|6 
    //  
    // (7) Colsize is present
    //     Some columns are specified in PERCENT, the rest are not specified.
    //     - Use the size as an approximate weighting, relative to 100, with a 
    //       minimum size of 1.
    //     - Columns with an unspecified width should fill up remaining space,
    //       e.g. 3 columns with 25%|25% should translate to 3|3|6
    //  
    // (8) Colsize is present
    //     Some columns are specified in PERCENT, some are specified in PIXELS.
    //     - Ingore the pixel values. Handle as above.
    //       (I don't have a good idea how to handle this case!) 
    //  

    // We will store the final widths in this array
    $w_array = array() ;

    if ( isset($colsize) ) 
    {
      // colsize is specified
     
      // Check for a percent symbol on any column 
	  $percent = ( strpos($colsize,'%') !== false ) ;
      
      // Count the total size and the number of unsized columns
      $tdsize   = explode("|", $colsize);
      $tdtotal  = 0 ;
      $tdnosize = 0 ;
      $s_array  = array() ;

      // There are two parts to this algorithm:
      // [1] Gathering information
      // [2] Setting the final column sizes

      // [1] Gathering information
      // In this stage we read the colsize values and initialize
      // $s_array   = colsize values
      // $tdtotal   = total weighting
      // $tdnosize  = count of columns without a specified size
      for ( $i=0 ; $i<$maxcols ; $i++ ) 
      {
        if ( isset ( $tdsize[$i] ) && ( trim ( $tdsize[$i] ) != '' ) ) 
        {
          $w = trim ( $tdsize[$i] ) ;
          if ( $w < 1 )
          {
            // treat 0 as unsized
            $s_array[$i] = 0 ;
            $tdnosize++ ;
          }
          else if ( $percent && ( strpos($w,'%') === false ) )
          {
            // Percentage mode, but percent symbol not present.
            $s_array[$i] = 0 ;
            $tdnosize++ ;
          }
          else
          {
            // Normal case. Save the width and increment the total.
            $s_array[$i] =  $w ;
            $tdtotal     += $w ;
          }  
        }
        else
        {
          // Size not specified for this column.
          $s_array[$i] = 0 ;
          $tdnosize++ ;
        }
      }

      if ( $percent )
      {
        // In percentage mode, the total wighting is always 100
        $tdtotal = 100 ;
      }
      
      // [2] Setting the final column sizes
      // In this stage we store the final column sizes in $w_array
      if ( ( $tdtotal + $tdnosize ) <= 12 )
      {
        // Use the values as specified.
        // Share the remaining space out among the unsized columns
        $remaining = 12 - $tdtotal ;
        $share     = ceil ( $remaining / $tdnosize ) ;
        
        for ( $i=0 ; $i<$maxcols ; $i++ ) 
        {
          if ( $s_array[$i] == 0 ) 
          {
            $w_array[$i] = ceil ( $remaining / $tdnosize ) ;
            $remaining   -= $w_array[$i] ;
            $tdnosize-- ;
          }
          else
          {
            $w_array[$i] = $s_array[$i] ; 
          }   
        }
      }
      else
      {
        // Use the values as approximate weightings
        // Start by assigning every column a width of 1
        for ( $i=0 ; $i<$maxcols ; $i++ ) 
        {
          $w_array[$i] = 1 ; 
        }
        
        // Now share out the rest
        $i = 0 ;
        $j = $maxcols ; 
        $h = 0 ;
        $pcfill = true ;
        
        while ( $j < 12 )
        {
          // Increment the width if it is underweight
          if ( ( $w_array[$i] / 12 ) < ( $s_array[$i] / $tdtotal ) )
          {
            $w_array[$i]++ ;
            $j++ ; 
          }
          
          // Increment column number and wraparound
          $i++ ;
          if ( $i >= $maxcols )
          {
            // Wraparound
            $i = 0 ;
            
            // $j must increase in each pass through the columns.
            if ( $h < $j )
            {
              // Store the current position
              $h = $j ;
            }
            else if ( $pcfill )
            {
              // In percentage mode, change 0% weighted columns to 100%
              // so that they take up the remaining space.
              $pcfill = false ;
              for ( $k=0 ; $k<$maxcols ; $k++ ) 
              {
                if ( $s_array[$k] == 0 )
                {
                  $s_array[$k] = 100 ;
                } 
              }
            }
            else
            {
              // We get here in percentage mode, if the size is specified for
              // all columns, but the total is less than 100%, e.g. two columns 
              // with 25%|25%, will result in 3|3.
              break ;
            }
          }    
        }
      }
    }
    else
    {
      // colsize is not specified
      // Share out the 12 units
      $remaining = 12  ;
      for ( $i=0 ; $i<$maxcols ; $i++ ) 
      {
        // Share among the remaining columns.
        // Round up as long as there is a remainder.
        // Eventually it will be an integer.
        $w_array[$i] =  ceil ( $remaining / ($maxcols-$i) ) ;
        $remaining   -= $w_array[$i] ; 
      }
    }

    //$result = "<div class='container-fluid" . ( !empty($customclass) ? " $customclass" : "") . "'>" ;
    $result = "<div" . ( !empty($customclass) ? " class='$customclass'" : "") . ">" ;

    foreach ($rows as $r) 
    {
      // Start of the row
      $result .= "<div class='row'>" ;

      $j=0 ; 
      while ( $j < $maxcols )
      {
        // Get the column width
        $w = $w_array[$j] ;
        
        // Get the content
        $c = ( isset($r[$j]) ) ? $r[$j] : "" ;
        
        // Remove first <ENTER> if exists.
        // Do not trim the line break from the end, because this affects the wiki parsing of the cell content.
        if (substr($c, 0, 2) == "\r\n") 
        {
          $c = substr($c, 2) ;
        }  
        
        if ( $joincols )
        {
          // Check for empty columns to the right
          for ( $k = $j+1 ; $k < $maxcols ; $k++ )
          {
            if ( isset($r[$k]) && ( trim ($r[$k]) != '' ) )
              break ;
            else
            {
              // Grab the space from the next column and skip it
              $j = $k ;  
              $w += $w_array[$j] ;
            }       
          }  
        }
        
        // Generate some output
        $result .= "<div class='col-" . $devicesize . "-" . $w . "'>\n" . $c . "</div>" ;

        // Increment the column number (because we are using while, not for)
        $j++ ;            
      }
      
      // End of the row
      $result .= "</div>";
    }

    // Close HTML table (no \n at end!)
    $result .= "</div>";

    return wikiplugin_fluidgrid_rollback($result, $hashes);
}
