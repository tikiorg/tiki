{* $Id: tiki-articles-js.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{jq notonready=true}
	var articleTypes = new Array();
{{foreach from=$types key=type item=properties}
	typeProp = new Array();
	{foreach from=$properties key=prop item=value}
		typeProp['{$prop|escape}'] = '{$value|escape}';
	{/foreach}
	articleTypes['{$type|escape}'] = typeProp;
{/foreach}}
{/jq}
