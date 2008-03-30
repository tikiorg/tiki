{* $Id$ *}

{if $prefs.feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Forum Posters{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Forum Posters{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="top_forum_posters" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<table width="90%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopForumPosters}
<tr>
<td class="module" width="50">{$modTopForumPosters[ix].name|avatarize}</td>
<td class="module">{$modTopForumPosters[ix].name}</td>
<td class="module" width="20">{$modTopForumPosters[ix].posts}</td>
</tr>
{/section}
</table>
{/tikimodule}
{/if}
