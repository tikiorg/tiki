<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/renderer_bytes.php,v 1.3 2005-11-03 14:43:19 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * Give back the kb change
 *
 */
class Text_Diff_Renderer_bytes extends Text_Diff_Renderer {

    function Text_Diff_Renderer_bytes()
    {
        $this->_addBytes = 0;
        $this->_delBytes = 0;
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
       $change= diffChar($orig, $final, 'bytes');
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
