{* $Id: file_backlinks.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
<div>
	<div class='box-title'>{if $file_backlinks_title}{tr}{$file_backlinks_title}{/tr}{else}{tr}Backlinks{/tr}{/if}</div>
	<div class='box-data'>
		<ul>
			{foreach from=$backlinks item=object}
				<li><a href="{$object.itemId|sefurl:$object.type}">{$object.name|escape}</a></li>
			{/foreach}
		</ul>
	</div>
</div>
