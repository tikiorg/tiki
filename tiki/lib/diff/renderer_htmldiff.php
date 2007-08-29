<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/renderer_htmldiff.php,v 1.1 2007-08-29 12:40:53 sept_7 Exp $

/**
 * HTML diff renderer.
 *
 * This class renders the diff of an HTML page with best effort.
 *
 * (c) 2007, Stéphane Casset <sept@logidee.com> and Coucil of Europe
 * Licence : LGPL v2.1
 */

include_once("Renderer.php");

class Text_Diff_Renderer_htmldiff extends Tiki_Text_Diff_Renderer {

    function Text_Diff_Renderer_htmldiff($context_lines = 0, $words = 0)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
	$this->_words = $words;
    }
    
    function _startDiff()
    {
        ob_start();
	$this->original = "";
	$this->final = "";
	$this->n = 0;
	$this->rspan = false;
	$this->lspan = false;
	$this->tracked_tags = array ("table","ul","div");
    }

    function _endDiff()
    {
	for ($i=0; $i <= $this->n; $i++) {
		if ($this->original[$i] != "" and $this->final[$i] != "") {
			echo "<tr><td width='50%' colspan='2' style='vertical-align:top'>".$this->original[$i]."</td><td width='50%' colspan='2' style='vertical-align:top'>".$this->final[$i]."</td></tr>\n";
		}
	}
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
    }

    function _endBlock()
    {
    }

    function _insert_tag($line,$tag,&$span) {
      $string = "";
	if ($line != '') {
          if (strstr($line,"<") === FALSE) {
       	    if ($span === false) {
  	      $string .= "<span class='$tag'>";
  	      $span = true;
  	    }
  	    $string .= $line;
  	  } else {
  	    if ($span === true) {
  	      $string .= "</span class='fin'>";
  	      $span = false;
  	    }
	    if (strstr($line,"class=")  === FALSE) {
               $string .= preg_replace("#<([^/> ]+)(.*[^/]?)?>#","<$1 class='$tag' $2>",$line);
	       $string = preg_replace("#<br class='(.*)'\s*/>#","<span class='$1'>&crarr;</span><br class='$1' />",$string);
	    } else {
               $string .= preg_replace("#<([^/> ]+)(.*)class=[\"']?([^\"']+)[\"']?(.*[^/]?)?>#","<$1$2 class='$3 $tag' $4>",$line);
	     }
          } 
	}
	return $string;
    }

    function _count_tags($line, $version) {
	preg_match("#<(/?)([^ >]+)#",$line,$out);
	if (in_array($out[2],$this->tracked_tags)) {
          if ($out[1] == '/') {
	    $this->tags[$version][$out[2]]--;
	  } else {
	    $this->tags[$version][$out[2]]++;
	  }
	}
    }

    function _can_break($line) {

      if (preg_match("#<(p|h\d|br)#",$line) == 0) {
        return false;
      }

      foreach($this->tags as $v) {
        foreach($v as $tag) {
          if ($tag != 0) {
	    return false;
	  }
	}
      }
      return true;
    }

    function _lines($type, $lines, $prefix = '')
    {
	static $context = 0;

	foreach($lines as $line) {
		switch($type) {
			case 'context':
				if ($context == 0 and $this->_can_break($line)) {
		  			$context = 1;
					$this->n++;
				}

				$this->_count_tags($line,'original');
				$this->_count_tags($line,'final');
				if ($this->lspan === true) {
					$this->original[$this->n] .= "</span>";
					$this->lspan = false;
				}
				if ($this->rspan === true) {
					$this->final[$this->n] .= "</span>";
					$this->rspan = false;
				}
				$this->original[$this->n] .= "$line";
				$this->final[$this->n] .= "$line";
				break;
			case 'change-added':
			case 'added':
				$this->_count_tags($line,'final');
				$this->final[$this->n] .= $this->_insert_tag($line,'diffadded',$this->rspan);
				$context = 0;
				break;
			case 'deleted':
			case 'change-deleted':
				$this->_count_tags($line,'original');
				$this->original[$this->n] .= $this->_insert_tag($line,'diffdeleted',$this->lspan);
				$context = 0;
				break;
		}
	
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
