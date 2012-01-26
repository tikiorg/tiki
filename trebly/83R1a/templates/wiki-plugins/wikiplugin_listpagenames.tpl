{* $Id: wikiplugin_listpagenames.tpl 34646 2011-05-27 02:35:57Z chealer $ *}
{strip}
<ul>
{section name=ix loop=$listpages}
<li>
	<a href="tiki-index.php?page={$listpages[ix].pageName|escape:"url"}" class="link" title="{tr}view{/tr}">
		{if !empty($showPageAlias) and $showPageAlias eq 'y' and !empty($listpages[ix].page_alias)}
			{$listpages[ix].page_alias}
		{else}
			{$listpages[ix].pageName}
		{/if}
	</a>
</li>
{/section}
</ul>
{/strip}