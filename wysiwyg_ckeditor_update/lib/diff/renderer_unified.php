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
 * "Unified" diff renderer.
 *
 * This class renders the diff in classic "unified diff" format.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer/unified.php,v 1.2 2004/01/09 21:46:30 chuck Exp $
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer_unified extends Tiki_Text_Diff_Renderer
{
    function Text_Diff_Renderer_unified($context_lines = 4)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
        $this->_table = Array();
    }
    function _endDiff() {
        return $this->_table;
    }

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        if ($xlen != 1) {
		$l = $xbeg+$xlen -1;
            $xbeg .= '-' . $l;
        }
        if ($ylen != 1) {
		$l = $ybeg+$ylen-1;
            $ybeg .= '-' . $l;
        }
        $this->_table[] =  array('type'=>"diffheader", 'old'=>"$xbeg", 'new'=>"$ybeg");
    }

    function _context($lines)
    {
        $this->_table[] = array('type'=>"diffbody", 'data'=>$lines);
    }
    function _added($lines)
    {
        $this->_table[] = array('type'=>"diffadded", 'data'=>$lines);
    }

    function _deleted($lines)
    {
        $this->_table[] = array('type'=>"diffdeleted", 'data'=>$lines);
    }

    function _changed($orig, $final)
    {
        $lines = diffChar($orig, $final, 0);
        $this->_deleted(array($lines[0]));
        $this->_added(array($lines[1]));
    }

}
