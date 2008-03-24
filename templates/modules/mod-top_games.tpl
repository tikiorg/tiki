{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_games.tpl,v 1.13 2007-10-14 17:51:02 mose Exp $ *}

{if $prefs.feature_games eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums neq 'y'}
{eval var="{tr}Top `$module_rows` games{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top games{/tr}" assign="tpl_module_title"}
{/if}
{/if}

{tikimodule title=$tpl_module_title name="top_games" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopGames}
<tr>{if $nonums neq 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-list_games.php?game={$modTopGames[ix].gameName}">{$modTopGames[ix].thumbName}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
