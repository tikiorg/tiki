<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/diff/difflib.php,v 1.15 2007-08-29 12:40:53 sept_7 Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once("lib/diff/Diff.php");
require_once("lib/diff/Renderer.php");

/* @brief modif tiki for the renderer lib	*/
class Tiki_Text_Diff_Renderer extends Text_Diff_Renderer {
     function _lines($lines, $prefix = '', $suffix = '')
//ADD $suffix
    {
        foreach ($lines as $line) {
            echo "$prefix$line$suffix\n";
        }
    }
  function render($diff)
    {
        $xi = $yi = 1;
        $block = false;
        $context = array();

        $nlead = $this->_leading_context_lines;
        $ntrail = $this->_trailing_context_lines;

        $this->_startDiff();

        foreach ($diff->getDiff() as $edit) {
            if (is_a($edit, 'Text_Diff_Op_copy')) {
                if (is_array($block)) {
                    if (count($edit->orig) <= $nlead + $ntrail) {
                        $block[] = $edit;
                    } else {
                        if ($ntrail) {
                            $context = array_slice($edit->orig, 0, $ntrail);
                            $block[] = &new Text_Diff_Op_copy($context);
                        }
                        $this->_block($x0, $ntrail + $xi - $x0,
                                      $y0, $ntrail + $yi - $y0,
                                      $block);
                        $block = false;
                    }
                }
                $context = $edit->orig;
            } else {
                if (!is_array($block)) {
//BUG if compare on all the length:                    $context = array_slice($context, count($context) - $nlead);
                    $context = array_slice($context, -$nlead, $nlead);
                    $x0 = $xi - count($context);
                    $y0 = $yi - count($context);
                    $block = array();
                    if ($context) {
                        $block[] = &new Text_Diff_Op_copy($context);
                    }
                }
                $block[] = $edit;
            }

            if ($edit->orig) {
                $xi += count($edit->orig);
            }
            if ($edit->final) {
                $yi += count($edit->final);
            }
        }

        if (is_array($block)) {
            $this->_block($x0, $xi - $x0,
                          $y0, $yi - $y0,
                          $block);
        }

        return $this->_endDiff();
    }
}

function diff2($page1, $page2, $type='sidediff') {
	if ($type == 'htmldiff') {
		global $tikilib;
		//$search = "#(<[^>]+>|\s*[^\s<]+\s*|</[^>]+>)#";
		$search = "#(<[^>]+>|[,\"':\s]+|[^\s,\"':<]+|</[^>]+>)#";
		preg_match_all($search,$page1,$out,PREG_PATTERN_ORDER);
		$page1 = $out[0];
		preg_match_all($search,$page2,$out,PREG_PATTERN_ORDER);
		$page2 = $out[0];
	} else {
		$page1 = split("\n", $page1);
		$page2 = split("\n", $page2);
	}
	$z = new Text_Diff($page1, $page2);
	if ($z->isEmpty()) {
		$html = '';
	} else {
		$context=2;
		$words=1;
		if (strstr($type,"-")) {
			list($type,$opt) = explode("-", $type, 2);
			if (strstr($opt,"full")) {
				$context=sizeof($page1);
			}
			if (strstr($opt,"char")) {
				$words=0;
			}
		}
//echo "<pre>";print_r($z);echo "</pre>";
		if ($type == 'unidiff') {
			require_once('renderer_unified.php');
			$renderer = new Text_Diff_Renderer_unified($context);
		} else if ($type == 'inlinediff') {
			require_once('renderer_inline.php');
			$renderer = new Text_Diff_Renderer_inline($context, $words);
		} else if ($type == 'sidediff') {
			require_once('renderer_sidebyside.php');
			$renderer = new Text_Diff_Renderer_sidebyside($context, $words);
		} else if ($type == 'bytes') {
			require_once('renderer_bytes.php');
			$renderer = new Text_Diff_Renderer_bytes();
		} else if ($type == 'htmldiff') {
			require_once('renderer_htmldiff.php');
			$renderer = new Text_Diff_Renderer_htmldiff(sizeof($page1));
		} else {
			return "";
		}
		$html = $renderer->render($z);
	}
	return $html;
}
/* @brief compute the characters differences between a list of lines
 * @param $orig array list lines in the original version
 * @param $final array the same lines in the final version
 */
function diffChar($orig, $final, $words=0, $function='character') {
	if ($words) {
		preg_match_all("/\w+\s+(?=\w)|\w+|\W/", implode("<br />", $orig), $matches);
		$line1 = $matches[0];
		preg_match_all("/\w+\s+(?=\w)|\w+|\W/", implode("<br />", $final), $matches);
		$line2 = $matches[0];
	} else {
		$line1 = preg_split('//', implode("<br />", $orig), -1, PREG_SPLIT_NO_EMPTY);
		$line2 = preg_split('//', implode("<br />", $final), -1, PREG_SPLIT_NO_EMPTY);
	}
	$z = new Text_Diff($line1, $line2);
	if ($z->isEmpty())
		return array($orig[0], $final[0]);
//echo "<pre>";print_r($z);echo "</pre>";
	require_once("renderer_$function.php");
      $new = "Text_Diff_Renderer_$function";
	$renderer = new $new(sizeof($line1));
	return $renderer->render($z);
}

// Tiki's current PHP requirement is 4.1, but is_a() requires PHP 4.2+,
// so we define it here if function doesn't exist
if (!function_exists('is_a')) {
	function is_a($object, $class_name) {
		$class = get_class($object);
		if ($class == $class_name) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
