<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
	}

	function render($name, $value, array $entry)
	{
		global $smarty;
		$smarty->loadPlugin('smarty_function_object_link');

		$arr = TikiLib::lib('categ')->getCategories();
		$list = '';
		
		foreach ($arr as $arx) {
			$myArr[$arx['categId']] = Array('parentId' => $arx['parentId'],'name' => $arx['name']);
		}

		if ($this->singleList == 'y') {

			foreach ($value as $ar) {
				if ($ar == 'orphan') {
					break;
				}

				$p_info = $myArr[$ar];
				if ( ($this->requiredParents=='all' || in_array($p_info['parentId'], $this->requiredParents)) && !in_array($p_info['parentId'], $this->excludeParents)) {
					$params = array('type' => 'category', 'id' => $ar);
					$link = smarty_function_object_link($params, $smarty);

					if (!empty($this->separator)) {
						$list .= $link . $this->separator;
					} else {
						if (empty($list)) {
							$list = "<ul class=\"categoryLinks\">";
						}
						$list .= ' <li>' . $link . "</li>";
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

				if ( ($this->requiredParents=='all' || in_array($p_info['parentId'], $this->requiredParents)) && !in_array($p_info['parentId'], $this->excludeParents)) {
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
		return '{HTML()}' . $list . '{HTML}';
	}
}
