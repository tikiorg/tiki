{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_games.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top games{/tr}" module_name="top_games"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopGames}
<tr><td width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td>
<td class="module">&nbsp;<a class="linkmodule" href="tiki-list_games.php?game={$modTopGames[ix].gameName}">{$modTopGames[ix].thumbName}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}