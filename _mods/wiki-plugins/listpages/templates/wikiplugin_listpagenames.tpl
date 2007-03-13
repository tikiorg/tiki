{* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/listpages/templates/wikiplugin_listpagenames.tpl,v 1.1 2007-03-13 17:36:05 sylvieg Exp $ *}
<ul>
{section name=ix loop=$listpages}
	<a href="tiki-index.php?page={$listpages[ix].pageName|escape:"url"}" class="link" title="{tr}view{/tr}">{$listpages[ix].pageName}</a>
{/section}
</ul>