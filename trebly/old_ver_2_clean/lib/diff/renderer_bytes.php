<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * Give back the kb change
 *
 */
class Text_Diff_Renderer_bytes extends Text_Diff_Renderer
{
    function Text_Diff_Renderer_bytes($first = -1)
    {
        $this->_addBytes = 0;
        $this->_delBytes = 0;
        $this->_first = $first;
    }
    function _endDiff() {
        return 'add='.$this->_addBytes.'&amp;del='.$this->_delBytes;
    }

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
     }

    function _added($lines)
    {
        $this->_addBytes += $this->_count($lines);
    }

    function _deleted($lines)
    {
        $this->_delBytes += $this->_count($lines);
    }

    function _changed($orig, $final)
    {
      if ($this->_first >= 0) { // stop recursion
            $this->_addBytes += count($final);
            $this->_delBytes += count($orig);
            return;
       }
       $change= diffChar($orig, $final, 0, 'bytes');
       preg_match("/add=([0-9]*)&amp;del=([0-9]*)/", $change, $matches);
       $this->_addBytes += $matches[1];
       $this->_delBytes += $matches[2];
    }
    function _count($lines)
    {
        $bytes = 0;
        foreach ($lines as $line) {
           $bytes += strlen($line);
        }
        return $bytes;
     }
}
