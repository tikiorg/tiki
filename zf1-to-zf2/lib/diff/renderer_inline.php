<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * "Inline" diff renderer.
 *
 * This class renders the diff in "inline" format,
 * with removed and inserted words for both versions
 *
 * @package Text_Diff
 */

require "renderer_sidebyside.php";

class Text_Diff_Renderer_inline extends Text_Diff_Renderer_sidebyside
{

    function Text_Diff_Renderer_inline($context_lines = 4, $words = 1)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
	$this->_words = $words;
    }

    function _block($xbeg, $xlen, $ybeg, $ylen, &$edits)
    {
	$this->_startBlock($this->_blockHeader($xbeg, $xlen, $ybeg, $ylen));
	$orig = array();
	$final = array();
	foreach ($edits as $edit) {
		if (is_array($edit->orig)) {
			$orig = array_merge($orig, $edit->orig);
		}
		if (is_array($edit->final)) {
			$final = array_merge($final, $edit->final);
		}
	}
	$lines = diffChar($orig, $final, $this->_words, "character_inline");
	echo "<tr class='diffbody'><td colspan='3'>$lines[0]</td></tr>\n";
	$this->_endBlock();
    }

}
