{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_games.tpl,v 1.6 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_file_galleries eq 'y'}
{tikimodule title="{tr}Top games{/tr}" name="top_games"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopGames}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-list_games.php?game={$modTopGames[ix].gameName}">{$modTopGames[ix].thumbName}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}