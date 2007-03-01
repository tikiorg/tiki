<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class PrintLib extends TikiLib {
	function PrintLib($db) {
		$this->TikiLib($db);
	}

function s_print_structure($structure_id) {

		return $this->s_toc_structure_tree2($structure_id);
		
	}
function s_toc_structure_tree2($structure_id, $level = 0) {
		global $dbTiki, $tikilib, $wikilib, $user;
		include_once "lib/structures/structlib.php";
		include_once "lib/wiki/wikilib.php";
		$structlib2 = new StructLib($this->db);
		$structure_tree = $structlib2->get_subtree($structure_id);
		$level = 0;
		$first = true;
		$ret="";
		$paginas = "";
		global $tikilib;
		
		foreach ( $structure_tree as $key=>$node ) {
			if ($node['last'] && !$node['first'])
				continue;

			$info = $tikilib->get_page_info($node['pageName']);
			$pdata = $tikilib->parse_data($info["data"]);
			$structure_tree[$key]["info"]=$info;
			$structure_tree[$key]["pdata"]=$pdata;
			$structure_tree[$key]['edit']=$tikilib->user_has_perm_on_object($user,$node['pageName'],'wiki page','tiki_p_edit');
			$structure_tree[$key]['editable']=$wikilib->is_editable($node['pageName'], $user, $info);
		}
		return $structure_tree;
	}
	
function s_toc_structure_tree($structure_id, $level = 0) {
		global $dbTiki;
		include "lib/structures/structlib.php";
		$structlib2 = new StructLib($this->db);
		$structure_tree = $structlib2->get_subtree($structure_id);
		$level = 0;
		$first = true;
		$ret="";
		$paginas = "";
		global $tikilib;
		
		foreach ( $structure_tree as $node ) {
		
			$info = $tikilib->get_page_info($node['pageName']);
			//print_r($info);
		
			//This special case indicates head of structure
			if ($node["first"] and $node["last"]) {
				$ret .= "<br/><br/><br/><br/><br/><br/><br/><br/><br/>";
				$ret .= "<center><div style=\"background-color: #EAEAEE;border: 2px solid #C4C5DE;width: 50%\">";
				$ret .= "<BR/><h1>".$info["description"]. "</h1>";
				$ret .= "<BR/><BR/><h4>(".$node['pageName'] . ")</h4>";
				$ret .= "<BR/></div></center><br/><br/><br/><br/><br/><br/><br/>";
				$ret .= "<br clear=all style='page-break-before:always'>";
				$ret .= "<h1>&Iacute;ndice</h1><BR/>";
				$ret .= "<a href=\"#".$node['pageName']."\">0&nbsp;".$info["description"]."&nbsp;(".$node['pageName'].")";
				$ret .= "</a><br/>";
				$paginas .= $this->codigoPagina("0",$node['pageName'],$info);
			}
			elseif ($node["first"] or !$node["last"]) {
				if ($node["first"] and !$first) {
			        $level++;
				}
				$first = false;
				for ($i = 0; $i < $level; $i++) {
					$ret .= "&nbsp;";
				}
				$ret .= "<a href=\"#".$node['pageName']."\">".$node['pos']."&nbsp;".$info["description"]."&nbsp;(".$node['pageName'].")";
				if (!empty($node['page_alias'])) {
					$ret .= "-" . $node['page_alias'];
				}
				$ret .="</a><br/>";
				$paginas .= $this->codigoPagina($node['pos'],$node['pageName'],$info);
			}
			//node is a place holder for last in level
			else {
				$level--;
			}
		}
		
		return $ret.$paginas;
	}
	
	function codigoPagina($pos,$pageName,$info){
		global $tikilib;
		//$info = $tikilib->get_page_info($pageName);
		$pdata = $tikilib->parse_data($info["data"]);
		$ret = "<br clear=all style='page-break-before:always'/>";
		$ret .= "<a name=\"".$pageName."\"></a><br/>";
		$ret .= "<BR/><div style=\"background-color: #EAEAEE;border: 1px solid #C4C5DE;width: 100%;\">";
		$ret .= "<h3>".$pos." ".$info["description"]."</h3><br/>";
		$ret .= "<b>".$pageName."</b>&nbsp; <i>(V.".$info["version"].")</i>";
		$ret .= "</div><BR/>";
		$ret .= $pdata;
		return $ret;	
	}
}
?>
