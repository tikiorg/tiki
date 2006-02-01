<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/renderer_inline.php,v 1.1 2006-02-01 21:06:13 jdrexler Exp $
/**
 * "Inline" diff renderer.
 *
 * This class renders the diff in "inline" format,
 * with removed and inserted words for both versions
 *
 * @package Text_Diff
 */

require "renderer_sidebyside.php";

class Text_Diff_Renderer_inline extends Text_Diff_Renderer_sidebyside {

    function Text_Diff_Renderer_inline($context_lines = 4, $words = 1)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
	$this->_words = $words;
    }
/*    
    function _startDiff()
    {
        ob_start();
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
        echo '</td><td>';
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
*/
    function _block($xbeg, $xlen, $ybeg, $ylen, &$edits)
    {
	$this->_startBlock($this->_blockHeader($xbeg, $xlen, $ybeg, $ylen));
	$orig = array();
	$final = array();
	foreach ($edits as $edit) {
		$orig = array_merge($orig, $edit->orig);
		$final = array_merge($final, $edit->final);
	}
	$lines = diffChar($orig, $final, $this->_words, "character_inline");
	echo "<tr class='diffbody'><td colspan='3'>$lines[0]</td></tr>\n";
	$this->_endBlock();
    }

}
