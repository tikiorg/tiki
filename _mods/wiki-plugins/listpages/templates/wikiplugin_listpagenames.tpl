{* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/listpages/templates/wikiplugin_listpagenames.tpl,v 1.2 2007-04-19 16:22:47 sylvieg Exp $ *}
<ul>
{section name=ix loop=$listpages}
	<a href="tiki-index.php?page={$listpages[ix].pageName|escape:"url"}" class="link" title="{tr}view{/tr}">
		{if !empty($showPageAlias) and $showPageAlias eq 'y' and !empty($listpages[ix].page_alias)}
			{$listpages[ix].page_alias}
		{else}
			{$listpages[ix].pageName}
		{/if}
	</a>
{/section}
</ul>