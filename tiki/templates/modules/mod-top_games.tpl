{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_games.tpl,v 1.7 2003-11-24 01:37:55 gmuslera Exp $ *}

{if $feature_file_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` games{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top games{/tr}" assign="tpl_module_title"}
{/if}

{tikimodule title=$tpl_module_title name="top_games"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopGames}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-list_games.php?game={$modTopGames[ix].gameName}">{$modTopGames[ix].thumbName}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
