{* $Id$ *}
<ul>
	{foreach from=$backlinks item=object}
		<li><a href="{$object.itemId|sefurl:$object.type}">{$object.name|escape}</a></li>
	{/foreach}
</ul>
