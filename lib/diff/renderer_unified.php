<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/renderer_unified.php,v 1.5 2004-08-27 21:04:18 sylvieg Exp $

/**
 * "Unified" diff renderer.
 *
 * This class renders the diff in classic "unified diff" format.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer/unified.php,v 1.2 2004/01/09 21:46:30 chuck Exp $
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer_unified extends Text_Diff_Renderer {

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
        $this->_deleted($orig);
        $this->_added($final);
    }

}
