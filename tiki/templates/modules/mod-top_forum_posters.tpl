{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_forum_posters.tpl,v 1.2 2004-05-01 01:06:34 damosoft Exp $ *}

{if $feature_forums eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Forum Posters{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Forum Posters{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="top_articles"}
<table width="90%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopForumPosters}
<tr>
<td class="module" width="50">{$modTopForumPosters[ix].name|avatarize}</td>
<td class="module">{$modTopForumPosters[ix].name}</td>
<td class="module" width="10">{$modTopForumPosters[ix].posts}</td>
</tr>
{/section}
</table>
{/tikimodule}
{/if}
