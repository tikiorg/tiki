{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{php}
	global $smarty;
	$listValues = $smarty->get_template_vars("listValues");
	$value = $smarty->get_template_vars("value");
	
	$types = array(
		"blog" => array(name=>"Blog",selected=>""),
		"calendar" => array(name=>"Calendar",selected=>""),
		"category" => array(name=>"Category",selected=>""),
		"faq" => array(name=>"FAQs",selected=>""),
		"file gallery" => array(name=>"File Gallery",selected=>""),
		"forum" => array(name=>"Forum",selected=>""),
		"image gallery" => array(name=>"Image Gallery",selected=>""),
		"quiz" => array(name=>"Quizze",selected=>""),
		"structure" => array(name=>"Structure",selected=>""),
		"sheet" => array(name=>"Sheet",selected=>""),
		"survey" => array(name=>"Survey",selected=>""),
		"tracker" => array(name=>"Tracker",selected=>""),
		"wiki page" => array(name=>"Wiki Page",selected=>"")
	);
	/*
		"assignments" => array(name=>"Assignments",selected=>""),
		"article" => array(name=>"Article",selected=>""),
		"poll" => array(name=>"Poll",selected=>""),
		"survey" => array(name=>"Survey",selected=>""),
		"tracker" => array(name=>"Tracker",selected=>""),
		*/
	if (isset($listValues) && $listValues!=""){
		foreach ($listValues as $key => $value) {
			$types[$value]["selected"]="selected";
		}
	}elseif (isset($value) && isset($types[$value])){
			$types[$value]["selected"]="selected";
	}
	$smarty->assign('types', $types);
{/php}

  {if $showlabel=="true"}<label for="{if $listName}{$listName}{else}createObjectType{/if}">{tr}Object type:{/tr}</label>{/if}
  <select name="{if $listName}{$listName}{else}createObjectType{/if}{if $multiple=="true"}[]{/if}" id="{if $listName}{$listName}{else}createObjectType{/if}" {if $multiple=="true"}multiple{/if} {if $listsize && $multiple=="true"}size="{$listsize}"{/if}>
      {foreach key=key item=type from=$types}
      	<option value="{$key}" {$type.selected} >{$type.name}</option>
      {/foreach}
  </select>

