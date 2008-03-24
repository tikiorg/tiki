<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/renderer_character.php,v 1.3 2005-05-18 11:00:35 mose Exp $

/**
 * "Side-by-Side" diff renderer.
 *
 * This class renders the diff in "side-by-side" format, like Wikipedia.
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer_character extends Tiki_Text_Diff_Renderer {
    var $orig;
    var $final;

    function Text_Diff_Renderer_character($context_lines = 0)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
        $this->orig = "";
        $this->final = "";
    }
    
    function _startDiff()
    {
    }

    function _endDiff()
    {
        return array($this->orig, $this->final);
    }

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
    }

    function _startBlock($header)
    {
        echo $header;
    }

    function _endBlock()
    {
    }

    function _lines($type, $lines, $prefix = '')
    {
    	if ($type == 'context') {
	        foreach ($lines as $line) {
			$this->orig .= $line;
			$this->final .= $line;
	        }
    	} elseif ($type == 'added' || $type == 'change-added') {
	        $l = "";
	        foreach ($lines as $line) {
			$l .= $line;
		 }
	        if (!empty($l))
	            $this->final .= '<span class="diffchar">'.$l."</span>";
    	} elseif ($type == 'deleted' || $type == 'change-deleted') {
	        $l = "";
	        foreach ($lines as $line)
			$l .= $line;
	        if (!empty($l))
	            $this->orig .= '<span class="diffchar">'.$l."</span>";
      }
    }

    function _context($lines)
    {
        $this->_lines('context', $lines);
    }

    function _added($lines, $changemode = FALSE)
    {
        if ($changemode) {
        	$this->_lines('change-added', $lines, '+');
        } else {
        	$this->_lines('added', $lines, '+');
        }
    }

    function _deleted($lines, $changemode = FALSE)
    {
        if ($changemode) {
        	$this->_lines('change-deleted', $lines, '-');
        } else {
	        $this->_lines('deleted', $lines, '-');
        }
    }

    function _changed($orig, $final)
    {
        $this->_deleted($orig, TRUE);
        $this->_added($final, TRUE);
    }

}
