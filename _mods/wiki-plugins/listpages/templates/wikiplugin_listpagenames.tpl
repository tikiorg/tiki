{* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/listpages/templates/wikiplugin_listpagenames.tpl,v 1.3 2007-04-19 16:33:25 sylvieg Exp $ *}
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