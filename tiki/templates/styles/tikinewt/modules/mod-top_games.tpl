{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-top_games.tpl,v 1.13 2007/10/14 17:51:02 mose *}

{if $prefs.feature_games eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums neq 'y'}
{eval var="{tr}Top `$module_rows` games{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top games{/tr}" assign="tpl_module_title"}
{/if}
{/if}

{tikimodule title=$tpl_module_title name="top_games" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopGames}
<li>
<a class="linkmodule" href="tiki-list_games.php?game={$modTopGames[ix].gameName}">{$modTopGames[ix].thumbName}</a></li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
