{* $Header: /cvsroot/tikiwiki/_mods/modules/blogroll/templates/modules/mod-blogroll.tpl,v 1.4 2006-07-10 21:57:33 amette Exp $ *}

{if $feature_file_galleries eq 'y'}
{eval var="{tr}Blogroll{/tr}" assign="tpl_module_title"}
{tikimodule title=$tpl_module_title name="blogroll" flip=$module_params.flip decorations=$module_params.decorations}
<a href="tiki-download_file.php?fileId={$fileId}">Get the OPML</a>
{if $nonums eq 'y'}
	<ul>
{else}
	<ol>
{/if}
{section name=ix loop=$feeds}
	{if $feeds[ix].type eq 'complete'}
		<li>
			<a href="{$feeds[ix].attributes.HTMLURL|regex_replace:"/\"/":"'"}" title="{$feeds[ix].attributes.DESCRIPTION|regex_replace:"/\"/":"'"}">{$feeds[ix].attributes.TITLE|escape}</a>
			<a href="{$feeds[ix].attributes.XMLURL|regex_replace:"/\"/":"'"}"><img alt="RSS" style="border: 0; vertical-align: text-bottom;" src="img/rss.png" /></a>
		</li>
	{elseif $feeds[ix].type eq 'open'}
		<li>
			{$feeds[ix].attributes.TEXT}
		{if $nonums eq 'y'}
			<ul>
		{else}
			<ol>
		{/if}
	{elseif $feeds[ix].type eq 'close'}
		{if $nonums eq 'y'}
			</ul>
		</li>
		{else}
			</ol>
		{/if}
	{/if}
{/section}
{if $nonums eq 'y'}
</ul>
{else}
</ol>
{/if}
{/tikimodule}
{/if}
