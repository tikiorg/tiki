<?php

/*
  TikiPackager module, copyright by Gongo aka Dimitri Smits 
  
  This file is part of TikiWiki, and is LGPLed. See the licence at http://www.fsf.org
  or the one accompanying tikiwiki
  
  Version: 0.1
*/

require_once("XML/Tree.php");

class ListingResult
{
    var $_count;
    var $_values;
    
    function ListingResult($count, &$values) 
    {
        $this->_count = $count;
        $this->_values = $values;
    }
    
    function get_listing_count() 
    {
        return $this->_count;
    }
    
    function get_listing_values() 
    {
        return $this->_values;
    }
}

class TikiPackagerLib extends TikiLib 
{
    var $_package;
    var $_is_dirty;
    
	function TikiPackagerLib($db) 
	{
		$this->set_DB($db, "Invalid db object passed to TikiPackagerLib constructor");
	}

	function set_DB($db, $deathmsg = 'Invalid db object passed!') 
	{
		if (!$db) {
			die ($deathmsg);
		}
		$this->db = $db;
	}
	
	function load_Draft($filename) 
	{
	}
	
	function create_Draft($filename) 
	{
	}
	
	function apply_changes() 
	{
	}
		
}

function fetch_manifest_dir( $number_of_rows = 0, $beginchar = '', $sort_mode = 1, $offset = 0, $searchstring = '' ) 
{

    $manifests = Array();
    $h = opendir( "packages/manifests" );
    if ($searchstring != '') 
    {
        $beginchar = '';
        $searchstring = strtolower($searchstring);
    }
    if ($beginchar != '') 
    {
        $beginchar = '[' . strtolower($beginchar) . '|' . strtoupper($beginchar) . ']';
    }
    while ($file = readdir($h)) 
    {
        if ($searchstring != '' ) 
        {
            if (preg_match('/.*' .$searchstring . '.*\.mf$/', strtolower($file))) 
            {
                $manifests[]=$file;
            }
        } 
        else 
        {
            if (preg_match( '/' . $beginchar . '.*\.mf$/', $file )) 
            {
                $manifests[]=$file;
            }
        }
    }
    closedir( $h );
    sort($manifests);
    
    $mfc = count($manifests);
    
    if ($number_of_rows <= 0) 
    {
        $number_of_rows = $mfc;
    }
    
    $ret_mf = array();
    $nor = $number_of_rows;
    
    if (is_numeric($offset) && ($offset >= 0) ) 
    {
       if (($offset*$nor) > $mfc) 
       {
           $offset = ceil($mfc / $nor);
       }
    } 
    else 
    {
        $offset = 0;
    }

    if ($sort_mode == 0) 
    {
        // sort descending
        $c_nor = $mfc - $offset*$nor;
        $c_nor_least = $c_nor - $nor;
        if ($c_nor_least < 0 ) 
        {
            $c_nor_least = 0;
        }
        while ($c_nor-- > $c_nor_least) 
        {
            $ret_mf[] = $manifests[$c_nor];
        }
    } 
    else 
    {
         // sort ascending, already done
        $c_nor = $offset*$nor;
        $c_nor_max = $offset+$nor;
        if ($c_nor_max > $mfc) 
        {
            $c_nor_max = $mfc;
        }
        while ( $c_nor < $c_nor_max ) 
        {
            $ret_mf[] = $manifests[$c_nor];
            $c_nor++;
        }
    }
    return new ListingResult(count($manifests), $ret_mf);
}

function remove_manifest($manifestName) 
{
  @unlink('packages/manifests/'.$manifestName);  
}

?>