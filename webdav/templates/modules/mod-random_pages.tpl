{* $Id$ *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Random Pages{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="random_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modRandomPages}
<li><a class="linkmodule" href="tiki-index.php?page={$modRandomPages[ix].pageName|escape:'url'}">{$modRandomPages[ix].pageName}</a></li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
