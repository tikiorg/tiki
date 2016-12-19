<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Categorylist extends Search_Formatter_ValueFormatter_Abstract
{
	private $requiredParents = array();
	private $excludeParents = array();
	private $singleList = 'y';
	private $separator;
	
	function __construct($arguments)
	{
		if (!empty($arguments['requiredParents'])) {
			$this->requiredParents = explode(',', $arguments['requiredParents']);
		} else {
			$this->requiredParents = 'all';
		}

		if (isset($arguments['excludeParents'])) {
			$this->excludeParents = explode(',', $arguments['excludeParents']);
		}
		
		if (isset($arguments['singleList'])) {
			$this->singleList = $arguments['singleList'];
		}		

		if (isset($arguments['separator'])) {
			$this->separator = $arguments['separator'];
		}
		
		if (isset($arguments['levelSeparator'])) {
			$this->levelSeparator = $arguments['levelSeparator'];
		} else {
			$this->levelSeparator = ":";
		}

		if (isset($arguments['useFullPath'])) {
			$this->useFullPath = $arguments['useFullPath'];
		} else {
			$this->useFullPath = "n";
		}
	}

	function render($name, $value, array $entry)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_object_link');

		$arr = TikiLib::lib('categ')->getCategories();
		$list = '';
		
		foreach ($arr as $arx) {
			$myArr[$arx['categId']] = Array('parentId' => $arx['parentId'],'name' => $arx['name'], 'tepath' => $arx['tepath']);
		}

		if ($this->singleList == 'y') {

			foreach ($value as $ar) {
				if ($ar == 'orphan') {
					break;
				}

				$p_info = $myArr[$ar];

				$showCat = $this->shouldShow($p_info['tepath']);

				if ($showCat) {
					if ($this->useFullPath == 'y'){
						$foundRoot = false;
						$printedPath = "";
						foreach ($p_info['tepath'] as $key=>$value) {
							if ($foundRoot || $this->requiredParents == "all") {
								$params = array('type' => 'category', 'id' => $key);
								$link = smarty_function_object_link($params, $smarty);
								if (empty($printedPath)) {
									$printedPath = $link;
								} else {
									$printedPath .= $this->levelSeparator . $link;
								}
							} elseif (in_array($key, $this->requiredParents)) {
								$foundRoot = true;
							}
						}
					} else {
						$printedPath = $p_info['name'];
					}

					if (!empty($this->separator)) {
						$list .= $printedPath. $this->separator;
					} else {
						if (empty($list)) {
							$list = "<ul class=\"categoryLinks\">";
						}
						$list .= ' <li>' . $printedPath . "</li>";
					}

				}
			}
			if (!empty($this->separator)) {
				$g = 0-strlen($this->separator);
				$list = substr($list, 0, $g);
			} else if (!empty($list)) {
				$list .= "</ul>";
			}
		} else {
			$parent  = array();

			foreach ($value as $ar) {
				if ($ar == 'orphan') {
					break;
				}

				$p_info = $myArr[$ar];

				$showCat = $this->shouldShow($p_info['parentId']);

				if ($showCat) {
					$parent[$p_info['parentId']][] = $ar;
				}
			}			
				
			foreach ($parent as $k=>$v) {
				if (empty($this->separator)) {
					$list .= "<h5>{$myArr[$k]['name']}</h5><ul class=\"categoryLinks\">";
					foreach ($v as $t) {
						$params = array('type' => 'category', 'id' => $t);
						$link = smarty_function_object_link($params, $smarty);
						$list .= "<li>".$link."</li>";
					}
					$list .= "</ul>";
				} else {
					$list .= "{$myArr[$k]['name']}: ";
					foreach ($v as $t) {
						$params = array('type' => 'category', 'id' => $t);
						$link = smarty_function_object_link($params, $smarty);
						$list .= $link.$this->separator;
					}					
				}
				if (!empty($this->separator)) {
					$g = 0-strlen($this->separator);
					$list = substr($list, 0, $g);
					$list .= "<br />";
				}
			}
		}
		return '~np~' . $list . '~/np~';
	}

	private function shouldShow($categoryPath) {
		//if it's not an array, it's simply an id
		if (!is_array($categoryPath)) {
			//set category path as an array with its only item as the id for both key and value.
			$categoryPath = array($categoryPath => $categoryPath);
		}

		$showCat = false;
		if ($this->requiredParents=='all') {
			$showCat = true;
		}
		foreach ($categoryPath as $key=>$val){
			if (in_array($key, $this->requiredParents)) {
				$showCat = true;
			}
		}
		foreach ($categoryPath as $key=>$val){
			if (in_array($key, $this->excludeParents)) {
				$showCat = false;
			}
		}

		return $showCat;
	}
}
