<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/renderer_sidebyside.php,v 1.10 2006-02-01 21:06:13 jdrexler Exp $

/**
 * "Side-by-Side" diff renderer.
 *
 * This class renders the diff in "side-by-side" format, like Wikipedia.
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer_sidebyside extends Tiki_Text_Diff_Renderer {

    function Text_Diff_Renderer_sidebyside($context_lines = 4, $words = 1)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
	$this->_words = $words;
    }
    
    function _startDiff()
    {
        ob_start();
        //echo '<table class="normal diff">';
    }

    function _endDiff()
    {
        echo '</table>';
        $val = ob_get_contents();
        ob_end_clean();
        return $val;
    }

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        return "$xbeg,$xlen,$ybeg,$ylen";
    }

    function _startBlock($header)
    {
        $h = split(",", $header);
        echo '<tr class="diffheader"><td colspan="2">';
        if ($h[1] == 1)
           echo tra('Line:')."&nbsp;".$h[0];
        else {
           $h[1] = $h[0]+$h[1]-1;
           echo tra('Lines:')."&nbsp;".$h[0].'-'.$h[1];
        }
        echo '</td><td colspan="2">';
        if ($h[3] == 1)
           echo tra('Line:')."&nbsp;".$h[2];
        else {
           $h[3] = $h[2]+$h[3]-1;
           echo tra('Lines:')."&nbsp;".$h[2].'-'.$h[3];
        }

        echo '</td></tr>';
    }

    function _endBlock()
    {
    }

    function _lines($type, $lines, $prefix = '')
    {
    	if ($type == 'context') {
	        foreach ($lines as $line) {
	        	if (!empty($line))
	            echo "<tr class='diffbody'><td>&nbsp;</td><td>$line</td><td>&nbsp;</td><td>$line</td></tr>\n";
	        }
    	} elseif ($type == 'added') {
	        foreach ($lines as $line) {
	        	if (!empty($line))
	            echo "<tr><td colspan='2'>&nbsp;</td><td class='diffadded'>$prefix</td><td class='diffadded'>$line</td></tr>\n";
	        }
    	} elseif ($type == 'deleted') {
	        foreach ($lines as $line) {
	        	if (!empty($line))
	            echo "<tr><td class='diffdeleted'>$prefix</td><td class='diffdeleted'>$line</td><td colspan='2'>&nbsp;</td></tr>\n";
	        }
    	} elseif ($type == 'change-deleted') {
    		echo '<tr><td class="diffdeleted" valign="top">'.$prefix.'</td><td class="diffdeleted" valign="top">'.implode("<br />", $lines)."</td>\n";
    	} elseif ($type == 'change-added') {
    		echo '<td class="diffadded" valign="top">'.$prefix.'</td><td class="diffadded" valign="top">'.implode("<br />", $lines)."</td></tr>\n";
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
        $lines = diffChar($orig, $final, $this->_words);
        $this->_deleted(array($lines[0]), TRUE);
        $this->_added(array($lines[1]), TRUE);
/* switch with these lines for no character diff
        $this->_deleted($orig, TRUE);
        $this->_added($final, TRUE);
*/
    }

}
