{* $Id$ *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` articles{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top articles{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="top_articles" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopArticles}
<li><a class="linkmodule" href="{$modTopArticles[ix].articleId|sefurl:article}">{$modTopArticles[ix].title}</a></li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
